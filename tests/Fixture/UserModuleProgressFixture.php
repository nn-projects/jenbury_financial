<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UserModuleProgressFixture
 */
class UserModuleProgressFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'user_module_progress';
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
                'module_id' => 1,
                'status' => 'Lorem ipsum dolor ',
                'created' => 1743379924,
                'modified' => 1743379924,
            ],
        ];
        parent::init();
    }
}
