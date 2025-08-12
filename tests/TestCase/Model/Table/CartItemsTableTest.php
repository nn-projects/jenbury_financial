<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CartItemsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CartItemsTable Test Case
 */
class CartItemsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CartItemsTable
     */
    protected $CartItems;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.CartItems',
        'app.Carts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CartItems') ? [] : ['className' => CartItemsTable::class];
        $this->CartItems = $this->getTableLocator()->get('CartItems', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CartItems);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CartItemsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\CartItemsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
