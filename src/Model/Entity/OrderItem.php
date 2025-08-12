<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * OrderItem Entity
 *
 * @property int $id
 * @property int $order_id
 * @property int $item_id
 * @property string $item_type
 * @property int $quantity
 * @property string $unit_price
 * @property string $item_total
 * @property string $discount_amount
 * @property string $final_price
 * @property string $item_status
 * @property string $refunded_amount
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\Order $order
 */
class OrderItem extends Entity
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
        'order_id' => true,
        'item_id' => true,
        'item_type' => true,
        'quantity' => true,
        'unit_price' => true,
        'item_total' => true,
        'discount_amount' => true,
        'final_price' => true,
        'item_status' => true,
        'refunded_amount' => true,
        'created' => true,
        'order' => true,
    ];
}
