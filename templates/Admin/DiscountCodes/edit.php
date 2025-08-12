<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DiscountCode $discountCode
 */
$this->Html->css(['admin/_discount-codes'], ['block' => true]);
$this->assign('title', 'Edit Discount Code');
?>
<div class="admin-edit-page discount-codes-edit-page"> <?php // Using admin-edit-page for potential specific future styling ?>

    <header class="admin-page-header">
        <h1><?= __('Edit Discount Code') . ': ' . h($discountCode->code) ?></h1>
        <div class="admin-back-link">
            <?= $this->Html->link(
                '‹ ' . __('Back to Discount Codes'),
                ['action' => 'index'],
                ['class' => 'button', 'escape' => false]
            ) ?>
        </div>
    </header>

    <div class="admin-form-container admin-content-card">
        <div class="admin-card-body">
            <?= $this->Form->create($discountCode) ?>
            <fieldset>
                <legend><?= __('Discount Code Details') ?></legend>
                <?php
                    echo $this->Form->control('code', [
                        'disabled' => true, // Code is not editable
                        'label' => 'Discount Code (Cannot be changed)'
                    ]);
                    echo $this->Form->control('percentage', [
                        'label' => 'Percentage*',
                        'type' => 'number',
                        'step' => '1', // Consistent with add page
                        'min' => 1,
                        'max' => 100,
                        'required' => true
                    ]);
                    echo $this->Form->control('is_active', [
                        'label' => 'Active Status (Tick if discount code is currently active)'
                    ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Save Changes'), ['class' => 'button button-admin-primary', 'style' => 'margin-top: 1.5rem;']) ?>
            <?= $this->Form->end() ?>

            <?php // Delete button is now outside the main form ?>
            <div class="form-buttons" style="margin-top: 1.5rem; display: flex; justify-content: flex-end; align-items: center;">
                <span class="postlink-button-wrapper wrapper-danger">
                    <?= __('Delete Discount Code') ?>
                    <?= $this->Form->postLink(
                        '', // No visible text for the input itself
                        ['action' => 'delete', $discountCode->id],
                        [
                            'confirm' => __('Are you sure you want to delete discount code "{0}" (ID: {1})?', h($discountCode->code), $discountCode->id),
                            'class' => 'post-link button-delete', // Class for potential JS hooks, styling is on wrapper
                            'escapeTitle' => false,
                            'title' => __('Delete Discount Code')
                        ]
                    ) ?>
                </span>
            </div>
        </div>
    </div>
</div>