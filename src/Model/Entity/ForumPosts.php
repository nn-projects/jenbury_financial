<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class ForumPosts extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected array $_accessible = [
        'forum_thread_id' => true,
        'user_id' => true,
        'content' => true,
        'created' => true,
        'modified' => true,
    ];
}