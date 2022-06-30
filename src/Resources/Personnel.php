<?php

namespace Iamfredric\EduAdmin\Resources;

/**
 * @property int $PersonnelId
 * @property string|null $Name
 * @property string|null $InternalName
 * @property string|null $Address
 * @property string|null $Zip
 * @property string|null $City
 * @property string|null $Country
 * @property string|null $Phone
 * @property string|null $Mobile
 * @property string|null $Email
 * @property string|null $Notes
 * @property string|null $Title
 * @property string|null $EmployeeNumber
 * @property string|null $ImageUrl
 * @property string|null $PriceCodeName
 * @property float|null $Cost
 * @property array<int,mixed>|null $Schedule
 * @property array<int,CustomField>|null $CustomFields
 */
class Personnel extends Resource
{
    protected array $casts = [
        'CustomFields.*' => CustomField::class
    ];
}
