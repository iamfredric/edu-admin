<?php

namespace Iamfredric\EduAdmin\Resources;

use Iamfredric\EduAdmin\Builder;
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
}
