<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        // Users table
        $table = $this->table('users');
        $table->addColumn('email', 'string', [
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('password', 'string', [
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('first_name', 'string', [
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('last_name', 'string', [
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('is_active', 'boolean', [
            'default' => true,
            'null' => false,
        ]);
        $table->addColumn('email_verified', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('role', 'string', [
            'default' => 'user',
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'null' => false,
        ]);
        $table->addIndex(['email'], ['unique' => true]);
        $table->create();

        // Courses table
        $table = $this->table('courses');
        $table->addColumn('title', 'string', [
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('description', 'text', [
            'null' => false,
        ]);
        $table->addColumn('image', 'string', [
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('price', 'decimal', [
            'precision' => 10,
            'scale' => 2,
            'null' => false,
        ]);
        $table->addColumn('is_active', 'boolean', [
            'default' => true,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'null' => false,
        ]);
        $table->create();

        // Modules table
        $table = $this->table('modules');
        $table->addColumn('course_id', 'integer', [
            'null' => false,
        ]);
        $table->addColumn('title', 'string', [
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('description', 'text', [
            'null' => false,
        ]);
        $table->addColumn('order', 'integer', [
            'null' => false,
        ]);
        $table->addColumn('price', 'decimal', [
            'precision' => 10,
            'scale' => 2,
            'null' => false,
        ]);
        $table->addColumn('is_active', 'boolean', [
            'default' => true,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'null' => false,
        ]);
        $table->addForeignKey('course_id', 'courses', 'id', [
            'delete' => 'CASCADE',
            'update' => 'CASCADE',
        ]);
        $table->create();

        // Contents table
        $table = $this->table('contents');
        $table->addColumn('module_id', 'integer', [
            'null' => false,
        ]);
        $table->addColumn('title', 'string', [
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('type', 'string', [
            'limit' => 50,
            'null' => false,
        ]);
        $table->addColumn('content', 'text', [
            'null' => false,
        ]);
        $table->addColumn('file_path', 'string', [
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('order', 'integer', [
            'null' => false,
        ]);
        $table->addColumn('is_active', 'boolean', [
            'default' => true,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'null' => false,
        ]);
        $table->addForeignKey('module_id', 'modules', 'id', [
            'delete' => 'CASCADE',
            'update' => 'CASCADE',
        ]);
        $table->create();

        // Purchases table
        $table = $this->table('purchases');
        $table->addColumn('user_id', 'integer', [
            'null' => false,
        ]);
        $table->addColumn('course_id', 'integer', [
            'null' => true,
        ]);
        $table->addColumn('module_id', 'integer', [
            'null' => true,
        ]);
        $table->addColumn('amount', 'decimal', [
            'precision' => 10,
            'scale' => 2,
            'null' => false,
        ]);
        $table->addColumn('payment_status', 'string', [
            'limit' => 50,
            'null' => false,
        ]);
        $table->addColumn('transaction_id', 'string', [
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('created', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('expires', 'datetime', [
            'null' => true,
        ]);
        $table->addForeignKey('user_id', 'users', 'id', [
            'delete' => 'CASCADE',
            'update' => 'CASCADE',
        ]);
        $table->addForeignKey('course_id', 'courses', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'CASCADE',
        ]);
        $table->addForeignKey('module_id', 'modules', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'CASCADE',
        ]);
        $table->create();
    }
}