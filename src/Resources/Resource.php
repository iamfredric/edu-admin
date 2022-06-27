<?php

namespace Iamfredric\EduAdmin\Resources;

use Iamfredric\EduAdmin\Builder;
use Iamfredric\EduAdmin\ResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @method static Builder where(string|callable $field, ?string $compare = null, mixed $value = null): static
 * @method static Builder orWhere(string|callable $field, ?string $compare = null, mixed $value = null): static
 * @method static Builder with(...$relations): static
 * @method static Builder getParams(?string $param = null): mixed
 * @method static Builder limit(int $limit): static
 * @method static Builder skip(int $skip): static
 * @method static Builder orderBy(string $orderBy, string $order = 'asc'): static
 * @method static Builder select(...$fields): static
 */
abstract class Resource extends Model
{
    public static function find(int $id): self
    {
        return new static(
            (new Builder('odata/' . static::resourceName() . "/{$id}"))->first()
        );
    }

    public static function query(): Builder
    {
        return new Builder('odata/' . static::resourceName(), static::class);
    }

    public static function all(): ResourceCollection|Collection
    {
        return (new Builder(
            'odata/' . static::resourceName(),
            static::class
        ))->get();
    }

    protected static function resourceName(): string
    {
        return Str::plural(static::singularResourceName());
    }

    public static function singularResourceName(): string
    {
        $parts = explode('\\', static::class);

        return end($parts);
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
}
