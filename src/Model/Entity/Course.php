<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Course Entity
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property float $price
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Module[] $modules
 * @property \App\Model\Entity\Purchase[] $purchases
 */
class Course extends Entity
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
        'title' => true,
        'description' => true,
        'image' => true,
        'price' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'modules' => true,
        'purchases' => true,
        'image_file' => true, // Allow mass assignment for the uploaded file object
    ];
}