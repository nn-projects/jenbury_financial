<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CartItemsFixture
 */
class CartItemsFixture extends TestFixture
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
                'cart_id' => 1,
                'item_id' => 1,
                'item_type' => 'Lorem ipsum dolor sit amet',
                'quantity' => 1,
                'price' => 1.5,
                'created' => '2025-04-29 16:13:20',
                'modified' => '2025-04-29 16:13:20',
            ],
        ];
        parent::init();
    }
}
