<?php

namespace Iamfredric\EduAdmin\Resources;

use Iamfredric\EduAdmin\Builder;

/**
 * @property int $ParticipantId
 * @property int $PriceNameId
 * @property int $Quantity
 * @property bool $Canceled
 * @property bool $WaitingForExportToLms
 * @property bool $ExportedToLms
 */
class UnnamedParticipant extends Resource
{
    public function cancel(): void
    {
        (new Builder(
            Participant::singularResourceName() . "/{$this->getKey()}/Cancel"
        ))->post();
    }

    public function getKeyName(): string
    {
        return 'ParticipantId';
    }
}
