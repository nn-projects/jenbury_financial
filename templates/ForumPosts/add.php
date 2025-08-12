<?php
$this->assign('title', 'Add Forum Post');
?>

<div class="add-module-page">
    <div class="module-container">
        <h2>Add New Forum Post to: <?= h($forumThread->title) ?></h2>
        <?= $this->Form->create($forumPost, ['class' => 'needs-validation', 'novalidate' => true]) ?>

        <?= $this->Form->hidden('forum_thread_id', ['value' => $forumThread->id]) ?>

        <div class="input">
            <?= $this->Form->control('content', [
                'type' => 'textarea',
                'label' => 'Content',
                'required' => true,
                'placeholder' => 'Enter text for your post!',
                'rows' => 5,
                'class' => 'form-input',
                'maxlength' => 150
            ]) ?>
        </div>

          
        <div class="submit">
            <?= $this->Form->button('Save Post', ['class' => 'button']) ?>

            <?= $this->Html->link('Cancel', 
                ['controller' => 'ForumThreads', 'action' => 'view', $forumThread->id], 
                ['class' => 'button']
            ) ?>

        </div>
        <?= $this->Form->end() ?>
    </div>
</div>