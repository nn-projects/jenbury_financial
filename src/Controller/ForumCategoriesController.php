<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

class ForumCategoriesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // Allow unauthenticated users to access the index and view actions
        $this->Authentication->addUnauthenticatedActions(['index', 'view']);
    }

    public function index()
    {
        $forumCategories = $this->ForumCategories->find()
            ->contain([
                'ForumThreads' => function ($q) {
                    return $q->where(['ForumThreads.is_approved' => 1]);
                }
            ])
            ->order(['ForumCategories.title' => 'ASC'])
            ->all();

        $this->set(compact('forumCategories'));
    }

    public function view($id = null)
    {
        $forumCategory = $this->ForumCategories->get(
            $id,
            contain: [
                'ForumThreads' => function ($q) {
                    return $q->where(['ForumThreads.is_approved' => true])
                        ->orderBy(['ForumThreads.is_sticky' => 'DESC','ForumThreads.created' => 'DESC'])
                        ->contain([
                            'Users',
                            'ForumPosts' => function ($q1) {
                                return $q1->where(['ForumPosts.is_approved' => true]);
                            }
                        ]); 
                }
                ]
            );

        $this->set(compact('forumCategory'));
    }
}