<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * OrdersFixture
 */
class OrdersFixture extends TestFixture
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
                'user_id' => 1,
                'total_amount' => 1.5,
                'subtotal_amount' => 1.5,
                'discount_amount' => 1.5,
                'discount_code' => 'Lorem ipsum dolor sit amet',
                'payment_status' => 'Lorem ipsum dolor sit amet',
                'refunded_amount' => 1.5,
                'transaction_id' => 'Lorem ipsum dolor sit amet',
                'payment_method' => 'Lorem ipsum dolor sit amet',
                'invoice_number' => 'Lorem ipsum dolor sit amet',
                'created' => '2025-04-29 16:13:40',
                'modified' => '2025-04-29 16:13:40',
            ],
        ];
        parent::init();
    }
}
