<?php
$this->assign('title', 'Edit Course');
?>
<?= $this->Html->css(['admin/_edit-course'], ['block' => true]); ?>

<div class="edit-course-page admin-edit-page"> <?php // Added admin-edit-page for consistency ?>
    
    <header class="admin-page-header">
        <h1><?= __('Edit Course: {0}', h($course->title)) ?></h1>
        <div class="admin-back-link">
            <?= $this->Html->link(
                '‹ ' . __('Back to Manage Courses'),
                ['action' => 'manageCourses'],
                ['class' => 'button', 'escape' => false] // General button class, can be styled further
            ) ?>
        </div>
    </header>

    <div class="admin-form-container admin-content-card"> <?php // Standardized container classes ?>
        <div class="admin-card-body"> <?php // Standardized card body ?>
            <?= $this->Form->create($course, ['type' => 'file', 'class' => 'needs-validation', 'novalidate' => true]) ?>
            <fieldset>
                <legend><?= __('Course Details') ?></legend> <?php // Added legend like discount codes ?>
                <div class="input">
                    <?= $this->Form->control('title', [
                        'required' => true,
                        'label' => 'Course Title',
                        'placeholder' => 'Enter course title',
                        'maxlength' => 150,
                        'class' => 'form-input'
                    ]) ?>
                    <?php // Removed validation-message div ?>
                </div>

                <div class="input"> <?php // Changed class ?>
                    <?= $this->Form->control('description', [
                        'type' => 'textarea',
                        'required' => true,
                        'label' => 'Course Description',
                        'placeholder' => 'Enter course description',
                        'maxlength' => 600,
                        'class' => 'form-input'
                    ]) ?>
                    <?php // Removed validation-message div ?>
                </div>

                <div class="input"> <?php // Changed class ?>
                    <?= $this->Form->control('price', [
                        'type' => 'number',
                        'id' => 'price-input',
                        'pattern' => '^\d+(?:[.]\d+)?$',
                        'step' => '0.01',
                        'required' => true,
                        'maxlength' => 8,
                        'min' => '0',
                        'label' => 'Price (AUD)',
                        'placeholder' => 'Enter course price',
                        'class' => 'form-input'
                    ]) ?>
                    <?php // Removed validation-message div ?>
                </div>

                <?php if ($course->image): ?>
                    <div class="input current-image"> <?php // Changed class ?>
                        <label>Current Course Image</label>
                        <?= $this->Html->image(str_replace('\\', '/', $course->image), ['style' => 'max-width: 200px; border-radius: 6px;']) ?>
                        <?= $this->Form->control('delete_image', ['type' => 'checkbox', 'label' => 'Delete current image', 'class' => 'form-checkbox']) ?>
                    </div>
                <?php endif; ?>

                <div class="input"> <?php // Changed class ?>
                    <?= $this->Form->control('image_file', [
                        'type' => 'file',
                        'label' => 'Upload New Image',
                        'required' => false,
                        'accept' => 'image/*',
                        'class' => 'form-input'
                    ]) ?>
                </div>

                <div class="form-group form-toggle">
                    <?= $this->Form->control('is_active', [
                        'type' => 'checkbox',
                        'label' => 'Mark as Active',
                        'class' => 'toggle-checkbox'
                    ]) ?>
                </div>
            </fieldset>

            <div class="form-actions step-actions"> <?php // Added step-actions for consistent button layout if desired ?>
                <?= $this->Form->button(__('Save Changes'), ['class' => 'button button-primary']) ?>
                <?= $this->Html->link(__('Cancel'), ['action' => 'manageCourses'], ['class' => 'button button-secondary']) ?>
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
</script>

