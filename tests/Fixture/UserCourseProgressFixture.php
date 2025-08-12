<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UserCourseProgressFixture
 */
class UserCourseProgressFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'user_course_progress';
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
                'course_id' => 1,
                'status' => 'Lorem ipsum dolor ',
                'last_accessed_content_id' => 1,
                'completion_date' => '2025-04-01 15:20:27',
                'created' => 1743520827,
                'updated' => 1743520827,
            ],
        ];
        parent::init();
    }
}
