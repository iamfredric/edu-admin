<?php

namespace Iamfredric\EduAdmin;

use Carbon\Carbon;
use Iamfredric\EduAdmin\Resources\Resource;
use Illuminate\Support\Collection;

/** @template T of Resource */
class Builder
{
    /**
     * @var array<int|string,string>
     */
    protected array $where = [];

    protected ?int $limit = null;

    protected ?int $skip = null;

    protected ?string $orderBy = null;

    protected string $order = 'asc';

    protected string $compare = 'AND';

    /**
     * @var array<int|string,mixed>
     */
    protected array $select = [];

    /**
     * @var array<string, string>
     */
    protected array $compareTranslations = [
        '=' => 'Eq',
        '!=' => 'Ne',
        '>' => 'Gt',
        '>=' => 'Ge',
        '<' => 'Lt',
        '<=' => 'Le',
        'IN' => 'in',
    ];

    /**
     * @var array<int|string, string>
     */
    protected array $relations = [];

    /** @param class-string<T>|null $resource */
    public function __construct(
        protected string $uri,
        protected ?string $resource = null,
        protected bool $withCount = true
    ) {
    }

    public function select(string ...$fields): static
    {
        $this->select = $fields;

        return $this;
    }

    public function when(mixed $statement, callable $callable): static
    {
        if ($statement) {
            $callable($this, $statement);
        }

        return $this;
    }

    public function whereRaw(string $statement): static
    {
        $this->where[] = $statement;

        return $this;
    }

    public function where(
        string|callable $field,
        ?string $compare = null,
        mixed $value = null
    ): static {
        if (is_callable($field)) {
            return $this->groupWhere($field);
        }

        if (strtolower($compare ?: '') === 'not in') {
            return $this->whereNotIn($field, $value);
        }

        if (is_null($value)) {
            $value = $compare;
            $compare = '=';
        }

        $compare = $this->compareTranslations[$compare] ?? $compare;

        if (is_array($value)) {
            $value = '(' . implode(',', $value) . ')';
        } elseif (is_string($value)) {
            $value = "'{$value}'";
        } elseif (is_bool($value)) {
            /** @phpstan-ignore-next-line */
            $value = boolval($value) === true ? 'true' : 'false';
        }

        $this->where[] = "{$field} {$compare} {$value}";

        return $this;
    }

    public function whereNull(string $field): static
    {
        $this->where[] = "{$field} Eq null";

        return $this;
    }

    public function whereNotNull(string $field): static
    {
        $this->where[] = "{$field} Ne null";

        return $this;
    }

    /**
     * @param string $field
     * @param mixed[] $values
     *
     * @return $this
     */
    public function whereNotIn(string $field, array $values): static
    {
        $value = implode(',', $values);

        $this->where[] = "not({$field} in ($value))";

        return $this;
    }

    /**
     * @param string $field
     * @param mixed[] $values
     *
     * @return $this
     */
    public function whereIn(string $field, array $values): static
    {
        $value = implode(',', $values);

        $this->where[] = "{$field} in ($value)";

        return $this;
    }

    public function whereDate(
        string $field,
        string $compare,
        Carbon|string $value
    ): static {
        $value = $value instanceof Carbon ? $value->toISOString() : $value;

        $compare = $this->compareTranslations[$compare] ?? $compare;

        $this->where[] = "{$field} {$compare} {$value}";

        return $this;
    }

    public function whereHas(string $field, ?callable $callable = null)
    {
        if ($callable) {
            $callable($builder = new Builder($this->uri, $this->resource));

            $query = $builder->getParams('$filter');
            $this->where[] = "{$field}/any(d:d/{$query})";
        } else {
            $this->where[] = "{$field}/any";
        }

        return $this;
    }

    protected function groupWhere(callable $callable): static
    {
        $callable($builder = new Builder($this->uri, $this->resource));

        $this->where[] = '(' . $builder->getParams('$filter') . ')';

        return $this;
    }

    public function orWhere(
        string|callable $field,
        ?string $compare = null,
        mixed $value = null
    ): static {
        $this->compare = 'OR';

        return $this->where($field, $compare, $value);
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function skip(int $skip): static
    {
        $this->skip = $skip;

        return $this;
    }

    public function orderBy(string $orderBy, string $order = 'asc'): static
    {
        $this->orderBy = $orderBy;
        $this->order = $order;

        return $this;
    }

    /**
     * @param array<string, callable>|string|callable ...$relations
     * @return $this
     */
    public function with(...$relations): static
    {
        $relations = array_map(function ($relation) {
            if (is_array($relation)) {
                $temporaryRelations = [];

                foreach ($relation as $name => $callable) {
                    $callable($builder = new Builder($this->uri, $this->resource, false));

                    $temporaryRelations[] = "{$name}({$builder->getQueryString(';')})";
                }

                $relation = implode(',', $temporaryRelations);
            } elseif (is_callable($relation)) {
                $e = $relation($builder = new Builder($this->uri, $this->resource, false));

                $relation = "{$e}({$builder->getQueryString(';')})";
            } else {
                if (str_contains($relation, '.')) {
                    $value = '';

                    foreach (array_reverse(explode('.', $relation)) as $index => $field) {
                        if ($index === 0) {
                            $value = $field;
                        } else {
                            $value = "{$field}(\$expand={$value})";
                        }
                    }

                    return $value;
                }
            }

            return $relation;
        }, $relations);

        $this->relations = [...$this->relations, ...$relations];

        return $this;
    }

    /**
     * @param array<string,mixed> $attributes
     * @return Collection
     */
    public function put(array $attributes = []): Collection
    {
        return (new Client())->put($this->uri, $attributes);
    }

    /**
     * @param array<string,mixed> $attributes
     * @return Collection
     */
    public function post(array $attributes = []): Collection
    {
        return (new Client())->post($this->uri, $attributes);
    }

    /**
     * @param array<int|string, string> $fields
     * @return ResourceCollection|Collection
     */
    public function get(array $fields = []): ResourceCollection|Collection
    {
        if (count($fields)) {
            $this->select(...$fields);
        }

        $response = (new Client())->get($this->uri, $this->getParams());

        if ($this->resource) {
            return new ResourceCollection(
                $response->get('value'),
                $response->get('@odata.count') ?: 0,
                $this->limit ?: 100000,
                $this->skip ?: 0,
                $this->orderBy,
                $this->order,
                $this->resource
            );
        }

        return $response;
    }

    public function first(): Collection
    {
        return (new Client())->get($this->uri, $this->getParams());
    }

    /** @return T|null */
    public function find(int $id): ?Resource
    {
        $this->uri = implode('/', [$this->uri, $id]);

        $response = (new Client())->get($this->uri, $this->getParams());

        if ($response->count() && $this->resource) {
            return new $this->resource($response);
        }

        return null;
    }

    public function getParams(?string $param = null): mixed
    {
        $attributes = [];

        if ($this->withCount) {
            $attributes['$count'] = 'true';
        }


        if (count($this->select)) {
            $attributes['$select'] = implode(',', $this->select);
        }

        if (count($this->where)) {
            $attributes['$filter'] = implode(
                " {$this->compare} ",
                $this->where
            );
        }

        if ($this->limit) {
            $attributes['$top'] = $this->limit;
        }

        if ($this->skip) {
            $attributes['$skip'] = $this->skip;
        }

        if ($this->orderBy) {
            $attributes['$orderBy'] = "{$this->orderBy} {$this->order}";
        }

        if (count($this->relations)) {
            $attributes['$expand'] = implode(',', $this->relations);
        }

        return empty($param) ? $attributes : $attributes[$param] ?? null;
    }

    public function getQueryString(string $separator = ''): string
    {
        return urldecode(http_build_query(
            array_filter($this->getParams()),
            '',
            $separator
        ));
    }
}
