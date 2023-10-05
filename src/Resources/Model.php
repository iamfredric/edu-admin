<?php

namespace Iamfredric\EduAdmin\Resources;

use ArrayAccess;
use Illuminate\Support\Collection;

abstract class Model implements ArrayAccess
{
    protected Collection $attributes;

    /**
     * @var array|class-string[]
     */
    protected array $casts = [];

    /**
     * @var array<string,mixed>
     */
    protected array $casted = [];

    /**
     * @param Collection<string,mixed>|array<string,mixed> $attributes
     */
    final public function __construct(array|Collection $attributes)
    {
        $this->attributes =
            $attributes instanceof Collection
                ? $attributes
                : new Collection($attributes);
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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes->toArray();
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {

        if (isset($this->casts[$name])) {
            if (! $value = $this->attributes->get($name, $default)) {
                return $default;
            }

            return $this->casted[$name] ??= new $this->casts[$name](
                $value
            );
        }

        if (isset($this->casts["{$name}.*"])) {
            return $this->casted[$name] ??= (new Collection(
                $this->attributes->get($name)
            ))->mapInto($this->casts["{$name}.*"]);
        }

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

    public function offsetExists($offset): bool
    {
        return $this->attributes->has($offset);
    }

    public function offsetGet($offset): mixed
    {
        return $this->getAttribute($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->setAttribute($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->attributes->offsetUnset($offset);
    }
}
