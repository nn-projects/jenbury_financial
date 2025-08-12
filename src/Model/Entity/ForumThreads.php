<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class ForumThreads extends Entity
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
        'forum_category_id' => true,
        'user_id' => true,
        'title' => true,
        'slug' => true,
        'post_count' => true,
        'last_post_id' => true,
        'last_post_user_id' => true,
        'last_post_created' => true,
        'is_locked' => true,
        'is_sticky' => true,
        'created' => true,
        'modified' => true,
    ];
}