<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Test Entity
 */
class TestEntity extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected array $_accessible = [
        '*' => true,
    ];
}