<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateEcommerceTables extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        // Carts table
        $this->table('carts')
            ->addColumn('user_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => false,
            ])
            ->addIndex(['user_id'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Cart Items table
        $this->table('cart_items')
            ->addColumn('cart_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('item_id', 'integer', [ // ID of course or module
                'null' => false,
            ])
            ->addColumn('item_type', 'string', [ // 'course' or 'module'
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('quantity', 'integer', [
                'default' => 1,
                'null' => false,
            ])
            ->addColumn('price', 'decimal', [ // Price at the time added to cart
                'precision' => 10,
                'scale' => 2,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => false,
            ])
            ->addIndex(['cart_id'])
            ->addForeignKey('cart_id', 'carts', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Orders table
        $this->table('orders')
            ->addColumn('user_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('total_amount', 'decimal', [ // Final amount paid after discounts
                'precision' => 10,
                'scale' => 2,
                'null' => false,
            ])
            ->addColumn('subtotal_amount', 'decimal', [ // Total amount before discounts
                'precision' => 10,
                'scale' => 2,
                'null' => false,
            ])
            ->addColumn('discount_amount', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'default' => 0.00,
                'null' => false,
            ])
            ->addColumn('discount_code', 'string', [
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('payment_status', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('refunded_amount', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'default' => 0.00,
                'null' => false,
            ])
            ->addColumn('transaction_id', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('payment_method', 'string', [
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('invoice_number', 'string', [
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => false,
            ])
            ->addIndex(['user_id'])
            ->addIndex(['invoice_number'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Order Items table
        $this->table('order_items')
            ->addColumn('order_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('item_id', 'integer', [ // ID of course or module
                'null' => false,
            ])
            ->addColumn('item_type', 'string', [ // 'course' or 'module'
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('quantity', 'integer', [
                'default' => 1,
                'null' => false,
            ])
            ->addColumn('unit_price', 'decimal', [ // Price of a single unit at purchase time
                'precision' => 10,
                'scale' => 2,
                'null' => false,
            ])
             ->addColumn('item_total', 'decimal', [ // unit_price * quantity
                'precision' => 10,
                'scale' => 2,
                'null' => false,
            ])
            ->addColumn('discount_amount', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'default' => 0.00,
                'null' => false,
            ])
            ->addColumn('final_price', 'decimal', [ // item_total - discount_amount
                'precision' => 10,
                'scale' => 2,
                'null' => false,
            ])
            ->addColumn('item_status', 'string', [
                'limit' => 50,
                'default' => 'purchased',
                'null' => false,
            ])
            ->addColumn('refunded_amount', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'default' => 0.00,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
            ])
            ->addIndex(['order_id'])
            ->addForeignKey('order_id', 'orders', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
