<?php

namespace Iamfredric\EduAdmin\Resources;

use Carbon\Carbon;
use Iamfredric\EduAdmin\Resources\Models\EventDate;

/**
 * @property-read int $EventId
 * @property-read string|null $EventName
 * @property-read int|null $OnDemandAccessDays
 * @property-read int $CourseTemplateId
 * @property-read string|null $CourseName
 * @property-read string|null $InternalCourseName
 * @property-read int $CategoryId
 * @property-read string|null $CategoryName
 * @property-read bool $ShowOnWeb
 * @property-read bool $ShowOnWebInternal
 * @property-read string|null $Notes
 * @property-read int|null $LocationId
 * @property-read string|null $City
 * @property-read Carbon $StartDate
 * @property-read Carbon $EndDate
 * @property-read int|null $MinParticipantNumber
 * @property-read int|null $MaxParticipantNumber
 * @property-read int $NumberOfBookedParticipants
 * @property-read int|null $ParticipantNumberLeft
 * @property-read int $StatusId
 * @property-read string|null $StatusText
 * @property-read string|null $AddressName
 * @property-read bool $ConfirmedAddress
 * @property-read int|null $CustomerId
 * @property-read bool $UsePriceNameMaxParticipantNumber
 * @property-read Carbon|null $ApplicationOpenDate
 * @property-read Carbon|null $LastApplicationDate
 * @property-read bool $CompanySpecific
 * @property-read bool $AllowOverlappingSessions
 * @property-read bool $HasPublicPriceName
 * @property-read string|null $PersonnelMessage
 * @property-read string|null $ProjectNumber
 * @property-read int $ParticipantVat
 * @property-read string|null $WebinarUrl
 * @property-read string|null $ExternalCourseUrl
 * @property-read string|null $BookingFormUrl
 * @property-read string|null $Created
 * @property-read string|null $Modified
 * @property-read bool $OnDemand
 * @property-read bool $OnDemandPublished
 * @property-read array|null $Personnel
 * @property-read array|null $Sessions
 * @property-read array|null $EventDates
 * @property-read array|null $Accessories
 * @property-read array<EventDate>|null $PaymentMethods
 * @property-read array|null $Answers
 * @property-read array|null $Files
 * @property-read array|null $Bookings
 * @property-read object|null $ResponsibleUser
 * @property-read array|null $Categories
 * @property-read LocationAddress|null $LocationAddress
 * @property-read array<PriceName>|null $PriceNames
 */
class Event extends Resource
{
    protected array $casts = [
        'StartDate' => Carbon::class,
        'EndDate' => Carbon::class,
        'LastApplicationDate' => Carbon::class,
        'ApplicationOpenDate' => Carbon::class,
        'EventDates.*' => EventDate::class,
        'Personnel.*' => Personnel::class,
        'LocationAddress' => LocationAddress::class,
        'PriceNames.*' => PriceName::class,
    ];
}
