<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

class ForumThreadsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // Allow unauthenticated users to access the index and view actions
        $this->Authentication->addUnauthenticatedActions(['view']);
    }


    public function view($id = null)
    {
        $forumThread = $this->ForumThreads->get(
            $id,
            contain: [
                'ForumCategories',
                'Users',
                'ForumPosts' => function ($q) {
                    return $q ->where(['ForumPosts.is_approved' => true])
                    ->orderBy(['ForumPosts.created' => 'DESC']); 
                },
                'ForumPosts.Users'
                ]
            );

        $this->set(compact('forumThread'));
    }

    public function add($forumCategoryId = null)
    {
        // Get the current user
        $user = $this->Authentication->getIdentity();
        if (!$user) {
            $this->Flash->error(__('You must be logged in to create a thread.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
        $userId = $user->getIdentifier();

        $forumCategories = $this->fetchTable('ForumCategories');
        $forumCategory = $forumCategories->get($forumCategoryId, ['contain' => []]);
        
        $forumThreads = $this->fetchTable('ForumThreads');
        $forumThread = $forumThreads->newEmptyEntity();

        if ($this->request->is('post')) {
            $forumThread = $forumThreads->patchEntity($forumThread, $this->request->getData(), [
                'accessibleFields' => ['user_id' => false, 'forum_category_id' => false]]);

            $forumThread -> user_id = $userId;
            $forumThread->forum_category_id = $forumCategoryId;

            if ($forumThreads->save($forumThread)) {
                $this->Flash->success(__('The forum thread has been added but has not been approved yet! Please wait for an admin to approve it.'));
                return $this->redirect(['controller' => 'ForumCategories', 'action' => 'view', $forumThread->forum_category_id]);
            }
            $this->Flash->error(__('The forum thread could not be added. Please, try again.'));
        }

        

        $this->set(compact('forumThread','forumCategory'));
    }


    public function edit($id = null)
    {
        $forumThreads = $this->fetchTable('ForumThreads');
        $forumThread = $forumThreads->get($id, contain: ['ForumCategories']);

        // Get the current user
        $user = $this->Authentication->getIdentity();
        $userId = $user->getIdentifier();

        if ($userId != $forumThread->user_id){
            throw new ForbiddenException(__('You are not allowed to edit a thread you do not own.'));
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $forumThread = $this->ForumThreads->patchEntity($forumThread, $this->request->getData());

            if ($forumThreads->save($forumThread)) {
                $this->Flash->success(__('The forum thread has been updated.'));
                return $this->redirect(['action' => 'view', $forumThread->id]);
            }
            $this->Flash->error(__('The forum thread could not be updated. Please, try again.'));        
        }

        $forumCategories = $this->fetchTable('ForumCategories')->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->toArray();

        $this->set(compact('forumThread','forumCategories'));
    }



}