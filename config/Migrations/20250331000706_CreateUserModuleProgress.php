<?php
declare(strict_types=1);

use Migrations\AbstractMigration; // Use AbstractMigration for more features

class CreateUserModuleProgress extends AbstractMigration // Extend AbstractMigration
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
        $table = $this->table('user_module_progress');
        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'limit' => 11, // Standard integer limit
            'null' => false,
            'signed' => true, // Explicitly set signed if needed, usually default
        ]);
        $table->addColumn('module_id', 'integer', [
            'default' => null,
            'limit' => 11, // Standard integer limit
            'null' => false,
            'signed' => true, // Explicitly set signed if needed, usually default
        ]);
        $table->addColumn('status', 'string', [
            'default' => 'not_started', // Set default status
            'limit' => 20, // Correct limit
            'null' => false,
        ]);
        // Add created and modified timestamps automatically
        $table->addTimestamps();

        // Add foreign key constraints
        $table->addForeignKey('user_id', 'users', 'id', [
            'delete' => 'CASCADE', // Or 'SET_NULL'/'RESTRICT' depending on desired behavior
            'update' => 'CASCADE',
        ]);
        $table->addForeignKey('module_id', 'modules', 'id', [
            'delete' => 'CASCADE', // Or 'SET_NULL'/'RESTRICT'
            'update' => 'CASCADE',
        ]);

        // Add a unique index on user_id and module_id
        $table->addIndex(['user_id', 'module_id'], [
            'name' => 'UNIQUE_USER_MODULE',
            'unique' => true,
        ]);

        // Remove the incorrect index added by bake (if it was added - check bake output/previous state)
        // If the incorrect index `20` was created, uncomment the line below:
        // $table->removeIndexByName('20');

        // Create the table with all definitions
        $table->create();
    }
}
