<?php

namespace Iamfredric\EduAdmin\Resources;

use Illuminate\Support\Carbon;

/**
 * @property int $ProgrammeId
 * @property string|null $ProgrammeName
 * @property string|null $InternalProgrammeName
 * @property int $CategoryId
 * @property string|null $CategoryName
 * @property bool $ShowOnWeb
 * @property bool $ShowOnWebInternal
 * @property int $ProgrammeStartId
 * @property string|null $ProgrammeStartName
 * @property int|null $MinParticipantNumber
 * @property int|null $MaxParticipantNumber
 * @property int $NumberOfBookedParticipants
 * @property int|null $ParticipantNumberLeft
 * @property Carbon $StartDate
 * @property Carbon $EndDate
 * @property Carbon|null $ApplicationOpenDate
 * @property Carbon $LastApplicationDate
 * @property bool $HasPublicPriceName
 * @property int $ParticipantVat
 * @property int|null $LocationId
 * @property int|null $LocationAddressId
 * @property string|null $City
 * @property string|null $AddressName
 * @property Carbon $Created
 * @property Carbon $Modified
 * @property string|null $ProjectNumber
 * @property string|null $ExternalCourseUrl
 * @property string|null $BookingFormUrl
 * @property array|null $Courses
 * @property array|null $Events
 * @property array|null $PaymentMethods
 * @property array|null $PriceNames
 * @property array|null $Answers
 * @property array|null $Bookings
 */
class ProgrammeStart extends Resource
{
    protected array $casts = [
        'StartDate' => Carbon::class,
        'EndDate' => Carbon::class,
        'ApplicationOpenDate' => Carbon::class,
        'LastApplicationDate' => Carbon::class,
        'Created' => Carbon::class,
        'Modified' => Carbon::class,
    ];
}
