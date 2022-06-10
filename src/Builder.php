<?php

namespace Iamfredric\EduAdmin;

use Iamfredric\EduAdmin\Resources\Resource;
use Illuminate\Support\Collection;

class Builder
{
    /**
     * @var array<int,string>
     */
    protected array $where = [];

    protected string $compare = 'AND';

    /**
     * @var array<string, string>
     */
    protected array $compareTranslations = [
        '=' => 'Eq',
        '!=' => 'Ne',
        '>' => 'Gt',
        '>=' => 'Ge',
        '<' => 'Lt',
        '<=' => 'Le'
    ];

    /**
     * @var array<int|string, string>
     */
    protected array $relations = [];

    /**
     * @param string $uri
     * @param class-string<Resource>|null $resource
     */
    public function __construct(
        protected string $uri,
        protected ?string $resource = null
    ) {
    }

    public function where(string|callable $field, ?string $compare = null, mixed $value = null): static
    {
        if (is_callable($field)) {
            return $this->groupWhere($field);
        }

        if (empty($value)) {
            $value = $compare;
            $compare = '=';
        }

        $compare = $this->compareTranslations[$compare] ?? $compare;

        if (is_string($value)) {
            $value = "'{$value}'";
        } elseif (is_bool($value)) {
            /** @phpstan-ignore-next-line */
            $value = boolval($value) === true ? 'true' : 'false';
        }

        $this->where[] = "{$field} {$compare} {$value}";

        return $this;
    }

    protected function groupWhere(callable $callable): static
    {
        $callable($builder = new Builder($this->uri, $this->resource));

        $this->where[] = '('.$builder->getParams('$filter').')';

        return $this;
    }

    public function orWhere(string|callable $field, ?string $compare = null, mixed $value = null): static
    {
        $this->compare = 'OR';

        return $this->where($field, $compare, $value);
    }

    /**
     * @param string ...$relations
     * @return $this
     */
    public function with(...$relations): static
    {
        $this->relations = [...$this->relations, ...$relations];

        return $this;
    }

    /**
     * @param array<string,mixed> $attributes
     * @return Collection
     */
    public function put(array $attributes = []): Collection
    {
        return (new Client())
            ->put($this->uri, $attributes);
    }

    public function get(): Collection
    {
        $response = (new Client())
            ->get($this->uri, $this->getParams());

        if ($this->resource) {
            return (new Collection($response->get('value')))->mapInto($this->resource);
        }

        return $response;
    }

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

        if (count($this->where)) {
            $attributes['$filter'] = implode(" {$this->compare} ", $this->where);
        }

        if (count($this->relations)) {
            $attributes['$expand'] = implode(',', $this->relations);
        }

        return empty($param) ? $attributes : $attributes[$param] ?? null;
    }
}
