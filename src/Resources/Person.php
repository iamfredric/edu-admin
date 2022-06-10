<?php

namespace Iamfredric\EduAdmin\Resources;

use Illuminate\Support\Collection;

/**
 * @property int $PersonId
 * @property int $CustomerId
 * @property string|null $FirstName
 * @property string|null $Address
 * @property string|null $Address2
 * @property string|null $Zip
 * @property string|null $City
 * @property string|null $Mobile
 * @property string|null $Phone
 * @property string|null $Email
 * @property string|null $CivicRegistrationNumber
 * @property string|null $Birthdate
 * @property string|null $EmployeeNumber
 * @property string|null $JobTitle string
 * @property string|null $Country
 * @property string|null $CountryCode
 * @property string|null $SsoId
 * @property string|null $LmaNumber
 * @property string|null $Created
 * @property string|null $Modified
 * @property bool $CanLogin
 * @property bool $IsContactPerson
 * @property string|null $PurchaseOrderNumber
 * @property string|null $Reference
 * @property int $IndividualId
 * @property Collection<int, CustomField>|null $CustomFields
 * @property array|null $Certificates
 * @property array|null $ExternalCertificates
 * @property array|null $Consents
 */
class Person extends WritableResource
{
    /**
     * @var array|class-string[]
     */
    protected array $casts = [
        'CustomFields.*' => CustomField::class
    ];

    protected static function resourceName(): string
    {
        return 'Persons';
    }
}
