<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UserModuleProgressTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UserModuleProgressTable Test Case
 */
class UserModuleProgressTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UserModuleProgressTable
     */
    protected $UserModuleProgress;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.UserModuleProgress',
        'app.Users',
        'app.Modules',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('UserModuleProgress') ? [] : ['className' => UserModuleProgressTable::class];
        $this->UserModuleProgress = $this->getTableLocator()->get('UserModuleProgress', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->UserModuleProgress);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\UserModuleProgressTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\UserModuleProgressTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
