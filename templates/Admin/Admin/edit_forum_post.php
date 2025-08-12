<?php
$this->assign('title', 'Edit Forum Post');
?>
<?= $this->Html->css('style', ['block' => true]) ?>

<div class="edit-module-page">
    <div class="module-container">
        <h2>Edit Forum Post: <?= h($forumPost->title) ?></h2>
            
        <?= $this->Form->create($forumPost, ['class' => 'needs-validation', 'novalidate' => true]) ?>

        <div class="input">
            <?= $this->Form->control('content', [
                'type' => 'textarea',
                'label' => 'Content',
                'required' => true,
                'placeholder' => 'Enter text for your post!',
                'rows' => 5,
                'class' => 'form-input',
                'maxlength' => 100
            ]) ?>
        </div>

        <div class="submit">
            <?= $this->Form->button('Save Changes', ['class' => 'button button-admin-primary']) ?>
            <?= $this->Html->link('Cancel', 
                ['action' => 'manageForumPosts'], 
                ['class' => 'button']
            ) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>