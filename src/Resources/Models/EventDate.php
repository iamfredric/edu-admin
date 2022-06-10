<?php

namespace Iamfredric\EduAdmin\Resources\Models;

use Carbon\Carbon;
use Iamfredric\EduAdmin\Resources\Model;

/**
 * @property int $EventDateId
 * @property Carbon $StartDate
 * @property Carbon $EndDate
 */
class EventDate extends Model
{
    /**
     * @var array|class-string[]
     */
    protected array $casts = [
        'StartDate' => Carbon::class,
        'EndDate' => Carbon::class
    ];
}
