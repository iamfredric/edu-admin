<?php

namespace Iamfredric\EduAdmin;

use ArrayIterator;
use Exception;
use Iamfredric\EduAdmin\Resources\Resource;
use Illuminate\Support\Collection;
use Traversable;

class ResourceCollection implements \IteratorAggregate, \ArrayAccess
{
    protected Collection $resources;

    /**
     * @param array<int, array<string, mixed>>|null $resources
     * @param int $total
     * @param int $limit
     * @param int $skip
     * @param string|null $orderBy
     * @param string $order
     * @param class-string $resource
     */
    public function __construct(
        ?array $resources,
        protected int $total,
        protected int $limit,
        protected int $skip,
        protected ?string $orderBy,
        protected string $order,
        protected string $resource
    ) {
        $this->resources = (new Collection($resources))->mapInto($this->resource);
    }

    public function total(): int
    {
        return $this->total;
    }

    public function next(): ResourceCollection
    {
        return $this->resource::query()
            ->when($this->orderBy, fn (Builder $query, $orderBy) => $query->orderBy($orderBy, $this->order))
            ->skip($this->skip + $this->limit)
            ->limit($this->limit)
            ->get();
    }

    public function prev(): ResourceCollection
    {
        return $this->resource::query()
            ->when($this->orderBy, fn (Builder $query, $orderBy) => $query->orderBy($orderBy, $this->order))
            ->skip($this->skip - $this->limit)
            ->limit($this->limit)
            ->get();
    }

    public function first(): Resource
    {
        return $this->resources->first();
    }

    public function hasMore(): bool
    {
        return $this->skip + $this->resources->count() < $this->total;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->resources->toArray());
    }

    public function offsetExists($offset): bool
    {
        return $this->resources->has($offset);
    }

    public function offsetGet($offset): Resource
    {
        return $this->resources->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->resources->put($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->resources->offsetUnset($offset);
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return $this->resources->map(fn (Resource $resource) => $resource->toArray())->toArray();
    }

    public function collect(): Collection
    {
        return $this->resources;
    }
}
