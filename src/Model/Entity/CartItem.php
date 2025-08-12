<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CartItem Entity
 *
 * @property int $id
 * @property int $cart_id
 * @property int $item_id
 * @property string $item_type
 * @property int $quantity
 * @property string $price
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Cart $cart
 */
class CartItem extends Entity
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
        'cart_id' => true,
        'item_id' => true,
        'item_type' => true,
        'quantity' => true,
        'price' => true,
        'created' => true,
        'modified' => true,
        'cart' => true,
    ];
}
