<?php

namespace Iamfredric\EduAdmin\Resources;

use Carbon\Carbon;
use Iamfredric\EduAdmin\Builder;

/**
 * @property int $ParticipantId
 * @property int $PersonId
 * @property string|null $FirstName
 * @property string|null $LastName
 * @property string|null $Address
 * @property string|null $Address2
 * @property string|null $Zip
 * @property string|null $City
 * @property string|null $Mobile
 * @property string|null $Phone
 * @property string|null $Email
 * @property string|null $CivicRegistrationNumber
 * @property Carbon|null $Birthdate
 * @property string|null $EmployeeNumber
 * @property string|null $JobTitle
 * @property string|null $Country
 * @property string|null $CountryCode
 * @property string|null $SsoId
 * @property string|null $LmaNumber
 * @property int $EventId
 * @property bool $WaitingForExportToLms
 * @property bool $ExportedToLms
 * @property Carbon $Created
 * @property Carbon $Modified
 * @property bool $Arrived
 * @property bool $Canceled
 * @property Carbon|null $CanceledDate
 * @property int|null $GradeId
 * @property float|null $GradeValue
 * @property string|null $GradeName
 * @property Carbon|null $GradeDate
 * @property bool $GradeAfterRetest
 * @property int $PriceNameId
 * @property string|null $WebinarUrl
 * @property string|null $LearnUrl
 * @property int $IndividualId
 * @property bool $Completed
 * @property array|null $Sessions
 * @property array|null $Answers
 * @property array|null $CourseEvaluations
 * @property array|null $EventDateArrivals
 * @property array|null $CustomFields
 * @property CourseTemplate $CourseTemplate
 * @property Customer $Customer
 */
class Participant extends Resource
{
    protected array $casts = [
        'Birthdate' => Carbon::class,
        'Modified' => Carbon::class,
        'CanceledDate' => Carbon::class,
        'GradeDate' => Carbon::class,
        'CourseTemplate' => CourseTemplate::class,
        'Customer' => Customer::class,
    ];

    public function cancel(): void
    {
        (new Builder(
            static::singularResourceName() . "/{$this->getKey()}/Cancel"
        ))->post();
    }
}
