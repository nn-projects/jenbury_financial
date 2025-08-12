<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateUserCourseProgress extends BaseMigration
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
        if ($this->hasTable('user_course_progress')) {
            $this->table('user_course_progress')->drop()->save();
        }

        $table = $this->table('user_course_progress');
        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('course_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('status', 'string', [
            'default' => 'not_started', // Default status
            'limit' => 20, // Limit for 'not_started', 'in_progress', 'completed'
            'null' => false,
        ]);
        $table->addColumn('last_accessed_content_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true, // Allow null as user might not have started
            'signed' => true, // Explicitly set signed if needed, usually default
        ]);
        $table->addColumn('completion_date', 'datetime', [
            'default' => null,
            'null' => true, // Completion date can be null
        ]);
        // Add created and modified timestamps automatically
        $table->addTimestamps();

        // Add foreign key constraints
        $table->addForeignKey('user_id', 'users', 'id', [
            'delete' => 'CASCADE', // Or 'SET_NULL'/'RESTRICT'
            'update' => 'CASCADE',
        ]);
        $table->addForeignKey('course_id', 'courses', 'id', [
            'delete' => 'CASCADE', // Or 'SET_NULL'/'RESTRICT'
            'update' => 'CASCADE',
        ]);
        $table->addForeignKey('last_accessed_content_id', 'contents', 'id', [
            'delete' => 'SET_NULL', // Set to null if content is deleted
            'update' => 'CASCADE',
        ]);
        
        // Add a unique index on user_id and course_id
        $table->addIndex(['user_id', 'course_id'], [
            'name' => 'UNIQUE_USER_COURSE',
            'unique' => true,
        ]);

        $table->create();
    }
}
