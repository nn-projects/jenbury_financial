<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserCourseProgres Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $course_id
 * @property string $status
 * @property int|null $last_accessed_content_id
 * @property \Cake\I18n\DateTime|null $completion_date
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $updated
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Course $course
 * @property \App\Model\Entity\Content $last_accessed_content
 */
class UserCourseProgress extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'user_id' => true,
        'course_id' => true,
        'status' => true,
        'last_accessed_content_id' => true,
        'completion_date' => true,
        'created' => true,
        'updated' => true,
        'user' => true,
        'course' => true,
        'last_accessed_content' => true,
    ];
}
