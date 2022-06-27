<?php

namespace Iamfredric\EduAdmin\Resources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $ProgrammeId
 * @property string|null $ProgrammeName
 * @property string|null $InternaProgrammeName
 * @property string|null $ProgrammeNumber
 * @property int $CategoryId
 * @property string|null $CategoryName
 * @property string|null $ProgrammeCode
 * @property int|null $MaxParticipantNumber
 * @property int|null $MinParticipantNumber
 * @property string|null $ImageUrl
 * @property string|null $ImageText
 * @property string|null $ImageComment
 * @property string|null $Description
 * @property string|null $DescriptionShort
 * @property string|null $CourseGoal
 * @property string|null $TargetGroup
 * @property string|null $CourseAfter
 * @property string|null $Prerequisites
 * @property string|null $Quote
 * @property string|null $Notes
 * @property int $InvoicePeriods
 * @property int $InvoicePeriodLength
 * @property int|null $Length
 * @property string|null $LengthUnit
 * @property string|null $PersonnelInfo
 * @property bool $ShowOnWeb
 * @property bool $ShowOnWebInternal
 * @property int $ParticipantVat
 * @property string|null $BusinessNumber
 * @property Carbon $Created
 * @property Carbon $Modified
 * @property Collection<int, ProgrammeStart>|null $ProgrammeStarts
 * @property Collection<int, CourseTemplate>|null $Courses
 * @property array|null $PriceNames
 * @property Collection|null $CustomFields
 * @property array|null $Categories
 * @property array|null $Subjects
 */
class Programme extends Resource
{
    protected array $casts = [
        'Created' => Carbon::class,
        'Modified' => Carbon::class,
        'Courses.*' => CourseTemplate::class,
        'CustomFields.*' => CustomField::class,
        'ProgrammeStarts.*' => ProgrammeStart::class,
    ];
}
