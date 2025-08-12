<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Order Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $total_amount
 * @property string $subtotal_amount
 * @property string $discount_amount
 * @property string|null $discount_code
 * @property string $payment_status
 * @property string $refunded_amount
 * @property string|null $transaction_id
 * @property string|null $payment_method
 * @property string|null $invoice_number
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\OrderItem[] $order_items
 */
class Order extends Entity
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
        'total_amount' => true,
        'subtotal_amount' => true,
        'discount_amount' => true,
        'discount_code' => true,
        'payment_status' => true,
        'refunded_amount' => true,
        'transaction_id' => true,
        'payment_method' => true,
        'invoice_number' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'order_items' => true,
    ];
}
