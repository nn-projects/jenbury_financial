<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContentBlock $contentBlock
 */
?>

<div class="content-blocks edit">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h1><?= __('Edit Content Block') ?></h1>
        <?= $this->Html->link(__('Back to Site Content'), ['action' => 'index'], ['class' => 'button button-outline']) ?>
    </div>

    <?= $this->Form->create($contentBlock) ?>
    <fieldset>
        <legend><?= __('Edit Content Block') ?></legend>
        <?php
            echo $this->Form->control('value', ['type' => 'textarea', 'required' => true, 'maxLength' => 100]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save Changes')) ?>
    <?= $this->Form->end() ?>

</div>