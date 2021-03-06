<?php

namespace Iamfredric\EduAdmin\Resources;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $BookingId
 * @property int $EventId
 * @property float|null $TotalPriceExVat
 * @property float|null $TotalPriceIncVat
 * @property float|null $VatSum
 * @property float|null $TotalDiscount
 * @property int $NumberOfParticipants
 * @property Carbon $Created
 * @property Carbon $Modified
 * @property bool $Paid
 * @property bool $Preliminary
 * @property int $PaymentMethodId
 * @property bool $Invoiced
 * @property string|null $Notes
 * @property string|null $Reference
 * @property string|null $PurchaseOrderNumber
 * @property Carbon|null $PostponedBillingDate
 * @property string|null $BookingSource
 * @property int|null $ProgrammeBookingId
 * @property Customer $Customer
 * @property Person $ContactPerson
 * @property Collection<Person>|null $Participants
 * @property array|null $UnnamedParticipants
 * @property array|null $Accessories
 * @property array|null $Answers
 * @property array|null $OrderRows
 * @property CourseTemplate|null $CourseTemplate
 */
class Booking extends WritableResource
{
    protected array $casts = [
        'Created' => Carbon::class,
        'Modified' => Carbon::class,
        'PostponedBillingDate' => Carbon::class,
        'CourseTemplate' => CourseTemplate::class,
        'Participants.*' => Person::class,
        'ContactPerson' => Person::class,
        'Customer' => Customer::class,
    ];
}
