<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UserContentProgressTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UserContentProgressTable Test Case
 */
class UserContentProgressTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UserContentProgressTable
     */
    protected $UserContentProgress;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.UserContentProgress',
        'app.Users',
        'app.Contents',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('UserContentProgress') ? [] : ['className' => UserContentProgressTable::class];
        $this->UserContentProgress = $this->getTableLocator()->get('UserContentProgress', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->UserContentProgress);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\UserContentProgressTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\UserContentProgressTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
