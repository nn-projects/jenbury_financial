<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter; // Required for MysqlAdapter::TEXT_LONG
use Migrations\BaseMigration;

class UpdateContentsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('contents');

        // Update the 'content' column to LONGTEXT
        $table->changeColumn('content', 'text', [
            'null' => true,
            'limit' => MysqlAdapter::TEXT_LONG, // Use LONGTEXT for large HTML content
        ]);

        // Optionally remove or deprecate unused columns
        if ($table->hasColumn('type')) {
            $table->removeColumn('type');
        }
        if ($table->hasColumn('file_path')) {
            $table->removeColumn('file_path');
        }

        $table->update();
    }
}