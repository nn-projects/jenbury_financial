<?php
$this->assign('title', 'Edit Forum Thread');
?>
<?= $this->Html->css('style', ['block' => true]) ?>


<div class="edit-module-page">
    <div class="module-container">
        <h2>Edit Forum Thread: <?= h($forumThread->title) ?></h2>
            
        <?= $this->Form->create($forumThread, ['class' => 'needs-validation', 'novalidate' => true]) ?>


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
            <?= $this->Form->button('Save Changes', ['class' => 'button button-admin-primary']) ?>
            <?= $this->Html->link('Cancel', 
                ['action' => 'manageForumThreads'], 
                ['class' => 'button']
            ) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>