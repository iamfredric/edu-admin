<?php

namespace Iamfredric\EduAdmin\Resources;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $ProgrammeBookingId
 * @property int $ProgrammeStartId
 * @property float|null $TotalPriceExVat
 * @property float|null $TotalPriceIncVat
 * @property float|null $number
 * @property float|null $VatSum
 * @property float|null $TotalDiscount
 * @property int $NumberOfParticipants
 * @property Carbon $Created
 * @property Carbon $Modified
 * @property bool $Preliminary
 * @property int $PaymentMethodId
 * @property string|null $Notes
 * @property string|null $PurchaseOrderNumber
 * @property Customer $Customer
 * @property Person $ContactPerson
 * @property Collection<Person>|null $Participants
 * @property array|null $OrderRows
 * @property array|null $Answers
 */
class ProgrammeBooking extends WritableResource
{
    protected array $casts = [
        'Created' => Carbon::class,
        'Modified' => Carbon::class,
        'Participants.*' => Person::class,
        'ContactPerson' => Person::class,
        'Customer' => Customer::class,
    ];
}
