<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DiscountCode $discountCode
 */
$this->Html->css(['admin/_discount-codes'], ['block' => true]);
$this->assign('title', 'Add Discount Code');
?>
<div class="admin-add-page discount-codes-add-page"> <?php // admin-add-page class is already here, good. ?>

    <header class="admin-page-header">
        <h1><?= __('Add New Discount Code') ?></h1>
        <div class="admin-back-link"> <?php // Removed inline style, margin is handled by .admin-back-link or .button now if needed ?>
            <?= $this->Html->link(
                '‹ ' . __('Back to Discount Codes'),
                ['action' => 'index'],
                ['class' => 'button', 'escape' => false] // Ensure it uses the .button class for new styling
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
                        'maxlength' => 20, // Increased maxlength for more flexible codes
                        'label' => 'Discount Code*',
                        'required' => true,
                        'placeholder' => 'E.g., SUMMER25OFF'
                    ]);
                ?>
                <p class="form-info-text"><?= __('Please note: Discount codes are case-insensitive. For example, "SUMMER25OFF" and "summer25off" will be treated as the same code.') ?></p>
                <?php
                    echo $this->Form->control('percentage', [
                        'label' => 'Percentage*',
                        'type' => 'number',
                        'step' => '1', // Allow whole numbers
                        'min' => 1,    // Min percentage usually 1
                        'max' => 100,  // Max 100
                        'required' => true,
                        'placeholder' => 'E.g., 10 for 10%'
                    ]);
                    // is_active is set to true by default in the controller,
                    // but we can add a toggle if needed in the future.
                    // echo $this->Form->control('is_active');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Save Discount Code'), ['class' => 'button button-admin-primary']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>