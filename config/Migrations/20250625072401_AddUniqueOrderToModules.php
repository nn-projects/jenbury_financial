<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddUniqueOrderToModules extends BaseMigration
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
        $table = $this->table('modules');
        $table->addIndex(['course_id', 'order'], ['unique' => true, 'name' => 'uq_course_display_order']);
        $table->update();
    }
}
