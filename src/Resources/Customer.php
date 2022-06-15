<?php

namespace Iamfredric\EduAdmin\Resources;

use Carbon\Carbon;

/**
 * @property int $CustomerId
 * @property string|null $CustomerName
 * @property string|null $CustomerNumber
 * @property string|null $Address
 * @property string|null $Address2
 * @property string|null $Zip
 * @property string|null $City
 * @property string|null $Country
 * @property string|null $CountryCode
 * @property string|null $OrganisationNumber
 * @property string|null $Email
 * @property string|null $Phone
 * @property string|null $Mobile
 * @property string|null $Notes
 * @property string|null $Web
 * @property string|null $GLN
 * @property Carbon $Created
 * @property Carbon $Modified
 * @property integer|null $CustomerGroupId
 * @property string|null $CustomerGroupName
 * @property boolean|null $NonCreditworthy
 * @property array|null $BillingInfo
 * @property \Illuminate\Support\Collection<int, CustomField>|null $CustomFields
 */
class Customer extends WritableResource
{
    protected array $casts = [
        'Created' => Carbon::class,
        'Modified' => Carbon::class,
        'CustomFields.*' => CustomField::class
    ];
}
