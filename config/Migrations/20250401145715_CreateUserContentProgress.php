<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateUserContentProgress extends BaseMigration
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
        // Drop the table if it exists to ensure idempotency
        if ($this->hasTable('user_content_progress')) {
            $this->table('user_content_progress')->drop()->save();
        }

        $table = $this->table('user_content_progress');
        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('content_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('status', 'string', [
            'default' => 'completed', // Default to completed as we only store completion records
            'limit' => 20, // Limit for 'completed'
            'null' => false,
        ]);
        // Add created and modified timestamps automatically
        $table->addTimestamps();

        // Add foreign key constraints
        $table->addForeignKey('user_id', 'users', 'id', [
            'delete' => 'CASCADE', // Or 'SET_NULL'/'RESTRICT'
            'update' => 'CASCADE',
        ]);
        $table->addForeignKey('content_id', 'contents', 'id', [
            'delete' => 'CASCADE', // Or 'SET_NULL'/'RESTRICT'
            'update' => 'CASCADE',
        ]);

        // Add a unique index on user_id and content_id
        $table->addIndex(['user_id', 'content_id'], [
            'name' => 'UNIQUE_USER_CONTENT',
            'unique' => true,
        ]);

        $table->create();
    }
}
