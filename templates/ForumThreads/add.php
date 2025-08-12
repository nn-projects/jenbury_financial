<?php
$this->assign('title', 'Add Forum Thread');
?>

<div class="add-module-page">
    <div class="module-container">
        <h2>Add New Forum Thread to: <?= h($forumCategory->title) ?></h2>
        <?= $this->Form->create($forumThread, ['class' => 'needs-validation', 'novalidate' => true]) ?>

        <?= $this->Form->hidden('forum_category_id', ['value' => $forumCategory->id]) ?>

        <div class="input"> <?php // Changed class ?>
            <?= $this->Form->control('title', [
                'required' => true,
                'placeholder' => 'Enter forum thread title',
                'maxlength' => 30,
                'class' => 'form-input'
            ]) ?>
            <?php // Removed validation-message div ?>
        </div>

          
        <div class="submit">
            <?= $this->Form->button('Save Forum Thread', ['class' => 'button']) ?>

            <?= $this->Html->link('Cancel', 
                ['controller' => 'ForumCategories', 'action' => 'view', $forumCategory->id], 
                ['class' => 'button']
            ) ?>

        </div>
        <?= $this->Form->end() ?>
    </div>
</div>