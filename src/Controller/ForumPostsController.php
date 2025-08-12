<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

class ForumPostsController extends AppController
{
    public function add($forumThreadId = null)
    {
        // Get the current user
        $user = $this->Authentication->getIdentity();
        $userId = $user->getIdentifier();
        if (!$user) {
            $this->Flash->error(__('You must be logged in to post.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
        
        $forumPosts = $this->fetchTable('ForumPosts');
        $forumPost = $forumPosts->newEmptyEntity();

        $forumThreads = $this->fetchTable('ForumThreads');
        $forumThread = $forumThreads->get($forumThreadId);

        if ($this->request->is('post')) {
            $forumPost = $forumPosts->patchEntity($forumPost, $this->request->getData());

            $forumPost->forum_thread_id = $forumThreadId;
            $forumPost -> user_id = $userId;    
            

            if ($forumPosts->save($forumPost)) {
                $forumThread->last_post_id = $forumPost->id;
                $forumThread->last_post_user_id = $forumPost->user_id;
                $forumThread->last_post_created = $forumPost->created;
                $forumThreads->save($forumThread);
                $this->Flash->success(__('The forum post has been added but has not been approved yet! Please wait for an admin to approve it.'));
                return $this->redirect(['controller' => 'ForumThreads',
                    'action' => 'view',
                    $forumPost->forum_thread_id]);
            }
            $this->Flash->error(__('The forum post could not be added. Please, try again.'));
        }

        $this->set(compact('forumPost', 'forumThread'));
    }

    public function edit($id = null)
    {
        $forumPosts = $this->fetchTable('ForumPosts');
        $forumPost = $forumPosts->get($id, [
            'contain' => ['ForumThreads']]);

        $forumThread = $forumPost->forum_thread;

        // Get the current user
        $user = $this->Authentication->getIdentity();
        $userId = $user->getIdentifier();

        if ($userId != $forumPost->user_id){
            throw new ForbiddenException(__('You are not allowed to edit a post you did not make.'));
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $forumPost = $forumPosts->patchEntity($forumPost, $this->request->getData());

            if ($forumPosts->save($forumPost)) {
                $this->Flash->success(__('The forum post has been updated.'));
                return $this->redirect(['controller' => 'ForumThreads',
                'action' => 'view',
                $forumPost->forum_thread_id]);
            }
            $this->Flash->error(__('The forum post could not be updated. Please, try again.'));        
        }

        $this->set(compact('forumPost', 'forumThread'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $forumPosts = $this->fetchTable('ForumPosts');
        $forumPost = $forumPosts->get($id);

        $forumThreads = $this->fetchTable('ForumThreads');
        $forumThreadID = $forumPost->forum_thread_id;

        // Get the current user
        $user = $this->Authentication->getIdentity();
        $userId = $user->getIdentifier();

        if ($userId != $forumPost->user_id){
            throw new ForbiddenException(__('You are not allowed to delete a post you did not make.'));
        }


        if ($forumPosts->delete($forumPost)) {
            $forumThread = $forumThreads->get($forumThreadID);

            $forumThread->post_count = $forumPosts->find()
                ->where(['forum_thread_id' => $forumThreadID])
                ->count();

            $lastPost = $forumPosts->find()
                ->where(['forum_thread_id' => $forumThreadID])
                ->order(['created' => 'DESC'])
                ->first();
            
            if ($lastPost) {
                $forumThread->last_post_id = $lastPost->id;
                $forumThread->last_post_user_id = $lastPost->user_id;
                $forumThread->last_post_created = $lastPost->created;
            } else {
                $forumThread->last_post_id = null;
                $forumThread->last_post_user_id = null;
                $forumThread->last_post_created = null;
            }
        
            $forumThreads->save($forumThread);

            $this->Flash->success(__('The forum post has been deleted.'));
        } else {
            $this->Flash->error(__('The forum post could not be deleted. Please, try again.'));
        }

        return $this->redirect(['controller' => 'ForumThreads' , 'action' => 'view', $forumThreadID]);
    }

}