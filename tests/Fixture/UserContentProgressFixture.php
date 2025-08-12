<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UserContentProgressFixture
 */
class UserContentProgressFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'user_content_progress';
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
                'content_id' => 1,
                'status' => 'Lorem ipsum dolor ',
                'created' => 1743520626,
                'updated' => 1743520626,
            ],
        ];
        parent::init();
    }
}
