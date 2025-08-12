<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class UpdateUserProgressTables extends AbstractMigration
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
        // Check if UserCourseProgress table exists
        if ($this->hasTable('user_course_progress')) {
            $table = $this->table('user_course_progress');
            
            // Add completion_date column if it doesn't exist
            if (!$table->hasColumn('completion_date')) {
                $table->addColumn('completion_date', 'datetime', [
                    'default' => null,
                    'null' => true,
                    'after' => 'last_accessed_content_id'
                ]);
            }
            
            $table->update();
        }

        // Check if UserModuleProgress table exists
        if ($this->hasTable('user_module_progress')) {
            $table = $this->table('user_module_progress');
            
            // Ensure created and modified columns exist
            if (!$table->hasColumn('created')) {
                $table->addColumn('created', 'datetime', [
                    'default' => 'CURRENT_TIMESTAMP',
                    'null' => false
                ]);
            }
            
            if (!$table->hasColumn('modified')) {
                $table->addColumn('modified', 'datetime', [
                    'default' => null,
                    'null' => true
                ]);
            }
            
            $table->update();
        }
    }
}