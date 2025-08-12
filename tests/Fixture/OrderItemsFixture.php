<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * OrderItemsFixture
 */
class OrderItemsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'order_id' => 1,
                'item_id' => 1,
                'item_type' => 'Lorem ipsum dolor sit amet',
                'quantity' => 1,
                'unit_price' => 1.5,
                'item_total' => 1.5,
                'discount_amount' => 1.5,
                'final_price' => 1.5,
                'item_status' => 'Lorem ipsum dolor sit amet',
                'refunded_amount' => 1.5,
                'created' => '2025-04-29 16:13:54',
            ],
        ];
        parent::init();
    }
}
