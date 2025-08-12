<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserModuleProgres Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $module_id
 * @property string $status
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Module $module
 */
class UserModuleProgress extends Entity
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
        'module_id' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'module' => true,
    ];
}
