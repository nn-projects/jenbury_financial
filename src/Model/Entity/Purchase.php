<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry; // Added for virtual field

/**
 * Purchase Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $course_id
 * @property int|null $module_id
 * @property float $amount
 * @property string $payment_status
 * @property string|null $transaction_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime|null $expires
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Course|null $course
 * @property \App\Model\Entity\Module|null $module
 */
class Purchase extends Entity
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
        'user_id' => true,
        'course_id' => true,
        'module_id' => true,
        'amount' => true,
        'payment_status' => true,
        'transaction_id' => true,
        'created' => true,
        'modified' => true,
        'expires' => true,
        'user' => true,
        'course' => true,
        'module' => true,
        // Note: 'order' will be accessible if the relationship is correctly loaded
        // 'order' => true, // Not strictly needed here if accessed via $this->order
    ];

    protected array $_virtual = ['actual_amount_paid'];

    protected function _getActualAmountPaid(): float
    {
        if ($this->order) { // Check if order is loaded
            $order = $this->order;
            // Scenario 1: Order has only one item, and it matches this purchase. Use Order's total_amount.
            if (!empty($order->order_items) && count($order->order_items) === 1) {
                $orderItem = $order->order_items[0];

                $itemType = null;
                $itemId = null;
                if (!empty($this->course_id)) {
                    $itemType = 'Course';
                    $itemId = $this->course_id;
                } elseif (!empty($this->module_id)) {
                    $itemType = 'Module';
                    $itemId = $this->module_id;
                }

                if ($itemType && $itemId && $orderItem->item_id == $itemId && strcasecmp($orderItem->item_type, $itemType) == 0) {
                    // For single-item orders that match the purchase, the order's total_amount is the most reliable final price.
                    return (float)$order->total_amount;
                }
            }

            // Scenario 2: Multi-item order, or single item didn't match (less likely if data is consistent).
            // Try to find the specific order_item and use its final_price.
            // Note: Based on the SQL dump, order_item.final_price might be the pre-discount item price.
            // This part of the logic will show that pre-discount item price if the order_item's final_price isn't updated.
            if (!empty($order->order_items)) {
                 $itemType = null;
                 $itemId = null;
                 if (!empty($this->course_id)) {
                     $itemType = 'Course';
                     $itemId = $this->course_id;
                 } elseif (!empty($this->module_id)) {
                     $itemType = 'Module';
                     $itemId = $this->module_id;
                 }
                 if ($itemType && $itemId) {
                     foreach ($order->order_items as $oi) {
                         if ($oi->item_id == $itemId && strcasecmp($oi->item_type, $itemType) == 0) {
                             return (float)$oi->final_price; // This is 10.00 for OrderItem 19 as per SQL dump
                         }
                     }
                 }
            }
        }
        // Final fallback: the amount stored on the purchase record itself.
        return (float)$this->amount;
    }
}