<?php

namespace Iamfredric\EduAdmin\Resources;

use Illuminate\Support\Collection;

/**
 * @property int $CourseTemplateId
 * @property string $EducationNumber
 * @property string|null $Shortening
 * @property string|null $CourseName
 * @property string|null $InternalCourseName
 * @property string|null $CourseDescription
 * @property string|null $CourseDescriptionShort
 * @property string|null $CourseGoal
 * @property string|null $TargetGroup
 * @property string|null $CourseAfter
 * @property string|null $Prerequisites
 * @property string|null $Quote
 * @property string|null $Notes
 * @property bool $ShowOnWeb
 * @property bool $ShowOnWebInternal
 * @property int $CategoryId
 * @property string|null $CategoryName
 * @property string|null $ImageUrl
 * @property string|null $ImageText
 * @property string|null $ImageComment
 * @property int $Days
 * @property string|null $StartTime
 * @property string|null $EndTime
 * @property bool $RequireCivicRegistrationNumber
 * @property string|null $Department
 * @property int|null $MaxParticipantNumber
 * @property int|null $MinParticipantNumber
 * @property int|null $CourseLevelId
 * @property int $ParticipantVat
 * @property int|null $SortIndex
 * @property bool $ExportToLms
 * @property string|null $BusinessNumber
 * @property string $Created
 * @property string $Modified
 * @property bool $OnDemand
 * @property int|null $OnDemandAccessDays
 * @property bool $DisablePersonnelPageGrading
 * @property Collection<int, Event>|null $Events
 * @property Collection<int, PriceName>|null $PriceNames
 * @property Collection<int, Subject>|null $Subjects
 * @property Collection<int, Category>|null $Categories
 * @property Collection<int, CustomField>|null $CustomFields
 * @property array|null $Files
 * @property array|null $Accessories
 */
class CourseTemplate extends Resource
{
    protected array $casts = [
        'CustomFields.*' => CustomField::class,
        'Events.*' => Event::class,
        'PriceNames.*' => PriceName::class,
        'Subjects.*' => Subject::class,
        'Categories.*' => Category::class,
    ];
}
