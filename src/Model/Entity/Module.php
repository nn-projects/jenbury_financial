<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Module Entity
 *
 * @property int $id
 * @property int $course_id
 * @property string $title
 * @property string $description
 * @property int $order
 * @property float $price
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Course $course
 * @property \App\Model\Entity\Content[] $contents
 * @property \App\Model\Entity\Purchase[] $purchases
 */
class Module extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected array $_accessible = [
        'course_id' => true,
        'title' => true,
        'description' => true,
        'order' => true,
        'price' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'course' => true,
        'contents' => true,
        'purchases' => true,
    ];
}