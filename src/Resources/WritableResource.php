<?php

namespace Iamfredric\EduAdmin\Resources;

use Iamfredric\EduAdmin\Builder;
use Iamfredric\EduAdmin\Client;
use Illuminate\Support\Collection;

abstract class WritableResource extends Resource
{
    /**
     * @param array<string,mixed> $arguments
     *
     * @return $this
     */
    public function update(array $arguments): static
    {
        (new Builder(
            'odata/' . static::resourceName() . "/{$this->getKey()}"
        ))->put($arguments);

        return $this;
    }

    /**
     * @param array<string,mixed> $attributes
     *
     * @return static
     */
    public static function create(array $attributes): static
    {
        $response = (new Builder(static::singularResourceName()))->post(
            $attributes
        );

        return new static($response);
    }

    public function save(): static
    {
        return $this->update($this->toArray());
    }

    public function delete(): bool
    {
        return self::destroy($this->getKey());
    }

    public static function destroy(int $id): bool
    {
        (new Client())->delete(static::singularResourceName() . '/' . $id);

        return true;
    }

    /**
     * @param mixed $id
     * @param array<string,mixed> $arguments
     * @return Collection
     */
    public static function updateWhereId(
        mixed $id,
        array $arguments
    ): Collection {
        return (new Builder('odata/' . static::resourceName() . "/{$id}"))->put(
            $arguments
        );
    }
}
