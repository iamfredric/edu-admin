<?php

namespace Iamfredric\EduAdmin\Resources;

use Iamfredric\EduAdmin\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Resource
{
    protected Collection $attributes;

    /**
     * @param Collection<string,mixed>|array<string,mixed> $attributes
     */
    final public function __construct(array|Collection $attributes)
    {
        $this->attributes = $attributes instanceof Collection ? $attributes : new Collection($attributes);
    }

    /**
     * @param array<string,mixed> $arguments
     *
     * @return $this
     */
    public function update(array $arguments): static
    {
        (new Builder("odata/".static::resourceName()."/{$this->getKey()}"))
            ->put($arguments);

        return $this;
    }

    public function save(): static
    {
        return $this->update($this->toArray());
    }

    /**
     * @param mixed $id
     * @param array<string,mixed> $arguments
     * @return Collection
     */
    public static function updateWhereId(mixed $id, array $arguments): Collection
    {
        return (new Builder("odata/".static::resourceName()."/{$id}"))
            ->put($arguments);
    }

    public function getKey(): mixed
    {
        return $this->attributes->get($this->getKeyName());
    }

    public function getKeyName(): string
    {
        $parts = explode('\\', static::class);
        $className = end($parts);

        return "{$className}Id";
    }

    public static function find(int $id): static
    {
        return new static(
            (new Builder("odata/".static::resourceName()."/{$id}"))
                ->get()
        );
    }

    public static function query(): Builder
    {
        return new Builder(
            "odata/".static::resourceName(),
            static::class
        );
    }

    public static function all(): Collection
    {
        return (new Builder(
            'odata/'.static::resourceName(),
            static::class
        ))->get();
    }

    protected static function resourceName(): string
    {
        $parts = explode('\\', static::class);
        $className = end($parts);

        return Str::plural($className);
    }

    /**
     * @param string $name
     * @param array<mixed> $arguments
     * @return Builder
     */
    public static function __callStatic(string $name, array $arguments): Builder
    {
        return static::query()->{$name}(...$arguments);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes->toArray();
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes->get($name, $default);
    }

    public function setAttribute(string $name, mixed $value): void
    {
        $this->attributes->put($name, $value);
    }

    public function __get(string $name): mixed
    {
        return $this->getAttribute($name);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->setAttribute($name, $value);
    }
}
