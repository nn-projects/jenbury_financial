<?php
$this->assign('title', 'Add Module');
?>
<?= $this->Html->css(['admin/_add-module'], ['block' => true]); ?>

<div class="add-module-page admin-add-page"> <?php // Added admin-add-page for consistency ?>

    <header class="admin-page-header">
        <h1><?= __('Add New Module to: {0}', h($course->title)) ?></h1>
        <div class="admin-back-link">
            <?= $this->Html->link(
                '‹ ' . __('Back to Manage Modules'),
                ['action' => 'manageModules', $course->id],
                ['class' => 'button button-primary', 'escape' => false]
            ) ?>
        </div>
    </header>

    <div class="admin-form-container admin-content-card">
        <div class="admin-card-body">
            <?= $this->Form->create($module, ['class' => 'needs-validation', 'novalidate' => true]) ?>
            <fieldset>
                <legend><?= __('Module Details') ?></legend>
                <div class="input">
                    <?= $this->Form->control('title', [
                        'required' => true,
                'placeholder' => 'Enter module title',
                'maxlength' => 75,
                'class' => 'form-input'
            ]) ?>
            <?php // Removed validation-message div ?>
        </div>

        <div class="input"> <?php // Changed class ?>
            <?= $this->Form->control('description', [
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Enter module description',
                'maxlength' => 300,
                'class' => 'form-input'
            ]) ?>
            <?php // Removed validation-message div ?>
        </div>

        <div class="input"> <?php // Changed class ?>
            <?= $this->Form->control('duration', [
                'type' => 'number',
                'label' => 'Duration (minutes)',
                'required' => true,
                'min' => '1',
                'maxlength' => 3,
                'id' => 'duration-input', // Added ID
                'placeholder' => 'Enter duration in minutes',
                'class' => 'form-input'
            ]) ?>
            <?php // Removed validation-message div ?>
        </div>

        <div class="input"> <?php // Changed class ?>
            <?= $this->Form->control('price', [
                'type' => 'number',
                'step' => '0.01',
                'id' => 'price-input',
                'pattern' => '^\d+(?:[.]\d+)?$',
                'required' => false, 
                'min' => '0',
                'placeholder' => 'Enter price (if sold separately)',
                'help' => 'Leave empty if included in course price',
                'class' => 'form-input'
            ]) ?>
            <?php // Removed validation-message div ?>
        </div>

        <div class="input"> <?php // Changed class ?>
            <?= $this->Form->control('is_active', [
                'type' => 'checkbox',
                'label' => 'Active',
                'checked' => true,
                'class' => 'toggle-checkbox'
            ]) ?>
        </div>

            
            </fieldset>
            
            <div class="form-actions step-actions"> <?php // Consistent button wrapper ?>
                <?= $this->Form->button(__('Save Module'), ['class' => 'button button-primary']) ?>
                <?= $this->Html->link(__('Cancel'),
                    ['action' => 'manageModules', $course->id],
                    ['class' => 'button button-secondary']
                ) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
    const priceInput = document.getElementById('price-input');

    // Prevent typing "-"
    priceInput.addEventListener('keypress', function(e) {
        if (e.key === '-' || e.key === 'e') {
            e.preventDefault();
        }
    });

    // Prevent pasting negative numbers
    priceInput.addEventListener('paste', function(e) {
        const clipboardData = e.clipboardData || window.clipboardData;
        const pastedText = clipboardData.getData('text');

        if (pastedText.includes('-')) {
            e.preventDefault();
        }
    });

     // Ensure max 2 decimal places
     priceInput.addEventListener('input', function(e) {
        let value = this.value;

        // Allow only up to 2 decimal places
        if (value.includes('.')) {
            let [integerPart, decimalPart] = value.split('.');
            if (decimalPart.length > 2) {
                this.value = integerPart + '.' + decimalPart.slice(0, 2);
            }
        }
    });

    priceInput.addEventListener('blur', function(e) {
        let value = this.value.trim();
        // If the value is a valid number and doesn't have a decimal, or has a trailing decimal
        if (value && !isNaN(parseFloat(value))) {
            const numValue = parseFloat(value);
            if (value.indexOf('.') === -1) { // Whole number
                this.value = numValue.toFixed(2);
            } else if (value.endsWith('.')) { // Ends with a dot e.g. "50."
                 this.value = numValue.toFixed(2);
            } else { // Has a decimal, ensure it's two places
                const parts = value.split('.');
                if (parts.length === 2) {
                    let decimalPart = parts[1];
                    if (decimalPart.length === 0) {
                        this.value = numValue.toFixed(2); // e.g. "50." becomes "50.00"
                    } else if (decimalPart.length === 1) {
                        this.value = numValue.toFixed(2); // e.g. "50.1" becomes "50.10"
                    }
                    // If decimalPart.length is 2 or more, toFixed(2) will handle it or it was already handled by input filter
                }
            }
        } else if (value === "") {
            // Optionally clear or set to "0.00" if empty on blur
            // this.value = "0.00";
        }
    });
</script>

<style>
.help {
    font-size: 0.9rem;
    color: #666;
    margin-top: 0.25rem;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const durationInput = document.getElementById('duration-input');

    if (durationInput) {
        // Prevent typing non-digit characters (allow backspace, delete, arrows etc.)
        durationInput.addEventListener('keydown', function(e) {
            // Allow: backspace, delete, tab, escape, enter
            if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress if not
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        // Prevent pasting non-digit characters
        durationInput.addEventListener('paste', function(e) {
            const clipboardData = e.clipboardData || window.clipboardData;
            const pastedText = clipboardData.getData('text');

            if (!/^\d+$/.test(pastedText)) { // Check if pasted text is only digits
                e.preventDefault();
            }
        });

        // Optional: Re-validate on input to remove leading zeros or enforce min=1
        durationInput.addEventListener('input', function(e) {
            // Example: Force minimum value if needed, though min="1" attribute helps
            // if (this.value !== '' && parseInt(this.value, 10) < 1) {
            //     this.value = '1';
            // }
        });
    }
});
</script>