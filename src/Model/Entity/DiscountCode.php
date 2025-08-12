<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DiscountCode Entity
 *
 * @property int $id
 * @property string $code
 * @property float $percentage
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class DiscountCode extends Entity
{
    protected array $_accessible = [
        'code'       => true,
        'percentage' => true,
        'is_active'  => true,
        'created'    => false,
        'modified'   => false,
    ];
}