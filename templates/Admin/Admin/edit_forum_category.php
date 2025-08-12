<?php
$this->assign('title', 'Edit Forum Category');
?>
<?= $this->Html->css('style', ['block' => true]) ?>

<div class="edit-module-page">
    <div class="module-container">
        <h2>Edit Forum Category: <?= h($forumCategory->title) ?></h2>
            
        <?= $this->Form->create($forumCategory, ['class' => 'needs-validation', 'novalidate' => true]) ?>

        <div class="input"> <?php // Changed class ?>
            <?= $this->Form->control('title', [
                'required' => true,
                'placeholder' => 'Enter forum category title',
                'maxlength' => 30,
                'class' => 'form-input'
            ]) ?>
            <?php // Removed validation-message div ?>
        </div>

        <div class="input"> <?php // Changed class ?>
            <?= $this->Form->control('description', [
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Enter forum category description',
                'maxlength' => 150,
                'class' => 'form-input'
            ]) ?>
            <?php // Removed validation-message div ?>
        </div>


        <div class="submit">
            <?= $this->Form->button('Save Changes', ['class' => 'button button-admin-primary']) ?>
            <?= $this->Html->link('Cancel', 
                ['action' => 'manageForumCategories'], 
                ['class' => 'button']
            ) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>