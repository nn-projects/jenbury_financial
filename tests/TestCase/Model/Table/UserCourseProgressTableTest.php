<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UserCourseProgressTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UserCourseProgressTable Test Case
 */
class UserCourseProgressTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UserCourseProgressTable
     */
    protected $UserCourseProgress;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.UserCourseProgress',
        'app.Users',
        'app.Courses',
        'app.LastAccessedContents',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('UserCourseProgress') ? [] : ['className' => UserCourseProgressTable::class];
        $this->UserCourseProgress = $this->getTableLocator()->get('UserCourseProgress', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->UserCourseProgress);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\UserCourseProgressTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\UserCourseProgressTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
