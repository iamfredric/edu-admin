<?php

namespace Iamfredric\EduAdmin\Resources;

use Carbon\Carbon;
use Iamfredric\EduAdmin\Builder;
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
 * @property Collection<Participant>|null $Participants
 * @property Collection<UnnamedParticipant>|null $UnnamedParticipants
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
        'Participants.*' => Participant::class,
        'UnnamedParticipants.*' => UnnamedParticipant::class,
        'ContactPerson' => Person::class,
        'Customer' => Customer::class,
    ];

    /**
     * @param array<array<string, mixed>> $participants
     * @return void
     */
    public function addParticipants(array $participants): void
    {
        $uri = implode('/', [
            self::singularResourceName(),
            $this->getKey(),
            'Participants',
        ]);
        (new Builder($uri))->post([
            'Options' => [
                'IgnoreIfPersonAlreadyBooked' => true,
                'ForceUsePostedPriceName' => false,
            ],
            'Participants' => $participants,
        ]);
    }

    public function addUnnamedParticipants(int|array $attributes): void
    {
        $uri = implode('/', [
            self::singularResourceName(),
            $this->getKey(),
            'UnnamedParticipants',
        ]);

        if (! is_array($attributes)) {
            $attributes = [[
                'Quantity' => $attributes,
            ]];
        }

        (new Builder($uri))->post($attributes);
    }

    /**
     * @param Collection<Person>|Person $persons
     * @param array{
     *  SkipDuplicateMatchOnPersons?: bool,
     *  IgnoreIfPersonAlreadyBooked?: bool,
     *  IgnoreMandatoryQuestions?: bool
     * } $options
     */
    public function nameUnnamedParticipants(
        Collection|Person $persons,
        int $priceNameId,
        array $options = [
            'SkipDuplicateMatchOnPersons' => true,
            'IgnoreIfPersonAlreadyBooked' => true,
            'IgnoreMandatoryQuestions' => true,
        ]
    ): void {
        $uri = implode('/', [
            self::singularResourceName(),
            $this->getKey(),
            'NameUnnamedParticipants',
        ]);

        if (! $persons instanceof Collection) {
            $persons = new Collection([$persons]);
        }

        (new Builder($uri))->post([
            'Options' => $options,
            'NamedUnnamedParticipants' => $persons->map(fn (Person $person) => [
                'PriceNameId' => $priceNameId,
                'PersonId' => $person->getKey(),
                'FirstName' => $person->FirstName,
                'LastName' => $person->LastName,
                'Address' => $person->Address,
                'Address2' => $person->Address2,
                'Zip' => $person->Zip,
                'City' => $person->City,
                'Mobile' => $person->Mobile,
                'Email' => $person->Email,
                'CivicRegistrationNumber' => $person->CivicRegistrationNumber,
                'Birthdate' => $person->Birthdate,
                'EmployeeNumber' => $person->EmployeeNumber,
                'JobTitle' => $person->JobTitle,
                'Country' => $person->Country,
                'CountryCode' => $person->CountryCode,
                'SsoId' => $person->SsoId,
            ])->toArray()
        ]);
    }

    /**
     * @param array<string, mixed> $participant
     * @return void
     */
    public function addParticipant(array $participant): void
    {
        $this->addParticipants([$participant]);
    }
}
