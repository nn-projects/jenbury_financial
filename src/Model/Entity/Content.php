<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Content Entity
 *
 * @property int $id
 * @property int $module_id
 * @property string $title
 * @property string $type
 * @property string $content
 * @property string|null $file_path
 * @property int $order
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Module $module
 */
class Content extends Entity
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
        'module_id' => true,
        'title' => true,
        'type' => true,
        'content' => true,
        'file_path' => true,
        'order' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'module' => true,
    ];
}