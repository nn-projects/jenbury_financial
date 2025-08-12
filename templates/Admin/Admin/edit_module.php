<![CDATA[<?php
$this->assign('title', 'Edit Module');
?>
<?= $this->Html->css(['admin/_edit-module'], ['block' => true]); ?>

<div class="edit-module-page admin-edit-page"> <?php // Added admin-edit-page ?>
    <header class="admin-page-header">
        <h1>Edit Module: <?= h($module->title) ?></h1>
        <div class="admin-back-link">
            <?= $this->Html->link(
                '‹ ' . __('Back to Manage Modules'),
                ['action' => 'manageModules', $module->course_id],
                ['class' => 'button', 'escape' => false]
            ) ?>
        </div>
    </header>
    <?php // Removed h4 for course title, it's in the main title or can be added back if needed ?>

    <div class="admin-form-container admin-content-card"> <?php // Standardized container ?>
        <div class="admin-card-body"> <?php // Standardized body ?>
            <?= $this->Form->create($module, ['class' => 'needs-validation', 'id' => 'editModuleForm', 'novalidate' => true]) ?>
            <fieldset>
                <legend><?= __('Module Details') ?></legend>
                <div class="input">
        <?= $this->Form->control('title', [
            'type' => 'text', // Changed back to text
            // 'id' => 'title-editor', // Removed ID for TinyMCE
            'required' => true,
            'placeholder' => 'Enter module title',
            'maxlength' => 75, // Added maxlength consistent with add_module
            'class' => 'form-input',
            'label' => 'Module Title'
        ]) ?>
    </div>

    <div class="input">
        <?= $this->Form->control('description', [
            'type' => 'textarea',
            'required' => true,
            'placeholder' => 'Enter module description',
            'maxlength' => 150,
            'class' => 'form-input'
        ]) ?>
    </div>

    <div class="input">
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
    </div>

    <div class="input"> <?php // This was .form-group form-toggle in add_course, using .input for now ?>
        <?= $this->Form->control('is_active', [
            'type' => 'checkbox',
            'label' => 'Active',
            'checked' => $module->is_active, // Use actual module status
            'class' => 'toggle-checkbox' // Ensure CSS for this exists or use standard form-check-input
        ]) ?>
    </div>
    </fieldset> <?php // Moved fieldset closing tag here ?>

    <div class="form-actions step-actions"> <?php // Standardized button wrapper ?>
        <?= $this->Form->button(__('Save Changes'), ['class' => 'button button-primary']) ?>
        <?= $this->Html->link(__('Cancel'),
            ['action' => 'manageModules', $module->course_id],
            ['class' => 'button button-secondary']
        ) ?>
    </div>
    <?= $this->Form->end() ?>
        </div> <?php // Close admin-card-body ?>
    </div> <?php // Close admin-form-container ?>
            
    <?php // Module Contents (Lessons) section - keep outside the main form card for now, or integrate if design allows ?>
    <?php if (!empty($module->contents)): ?>
    <div class="module-contents admin-content-card" style="margin-top: 2rem;"> <?php // Added admin-content-card and margin ?>
        <div class="admin-card-header"> <?php // Added card header ?>
            <h3>Module Contents (Lessons)</h3>
        </div>
        <div class="admin-card-body"> <?php // Added card body ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($module->contents as $content): ?>
                        <tr>
                            <td><?= $content->order ?></td>
                            <td><?= h($content->title) ?></td>
                            <td><?= h($content->content_type) ?></td>
                            <td class="actions">
                                <?= $this->Html->link('Edit', 
                                    ['action' => 'editLessonContent', $content->id],
                                    ['class' => 'button button-small']
                                ) ?>
                                <?= $this->Form->postLink('Delete', 
                                    ['action' => 'deleteContent', $content->id],
                                    [
                                        'confirm' => 'Are you sure you want to delete this content?',
                                        'class' => 'button button-small button-danger'
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div> <?php // Close admin-card-body for contents ?>
    </div> <?php // Close module-contents admin-content-card ?>
    <?php endif; ?>
</div> <?php // Close edit-module-page ?>

<!-- TinyMCE script load removed -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // TinyMCE for Title - REMOVED
    // if (document.getElementById('title-editor')) {
    //     tinymce.init({
    //         selector: '#title-editor',
    //         menubar: false,
    //         toolbar: 'undo redo | bold italic underline strikethrough | removeformat',
    //         plugins: 'autoresize wordcount',
    //         autoresize_bottom_margin: 20,
    //         height: 150, // Adjust height as needed for a title field
    //         setup: function (editor) {
    //             editor.on('init', function () {
    //                 // Optional: Set a max character limit visually or via events
    //                 // This is a basic example, more robust limiting might need a plugin
    //                 // or more complex event handling if strict char count is needed.
    //             });
    //         }
    //     });
    // }

    // Existing scripts for price and order inputs
    const priceInput = document.getElementById('price-input');
    if (priceInput) {
        priceInput.addEventListener('keypress', function(e) {
            if (e.key === '-' || e.key === 'e') {
                e.preventDefault();
            }
        });
        priceInput.addEventListener('paste', function(e) {
            const clipboardData = e.clipboardData || window.clipboardData;
            const pastedText = clipboardData.getData('text');
            if (pastedText.includes('-')) {
                e.preventDefault();
            }
        });
        priceInput.addEventListener('input', function(e) {
            let value = this.value;
            if (value.includes('.')) {
                let [integerPart, decimalPart] = value.split('.');
                if (decimalPart.length > 2) {
                    this.value = integerPart + '.' + decimalPart.slice(0, 2);
                }
            }
        });
    }

    // Save TinyMCE content before form submission
    var editModuleForm = document.getElementById('editModuleForm');
    if (editModuleForm) {
      editModuleForm.addEventListener('submit', function(e) {
        // Removed TinyMCE save for title-editor
        // if (typeof tinymce !== 'undefined' && tinymce.get('title-editor')) {
        //   tinymce.get('title-editor').save();
        // }
      });
    }
});
</script>

<style>
.help {
    font-size: 0.9rem;
    color: #666;
    margin-top: 0.25rem;
}

.module-contents {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #ddd;
}

.button-small {
    padding: 0.3rem 0.6rem;
    font-size: 0.9rem;
}

.button-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.button-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

/* Optional: Adjust styling for TinyMCE if needed */
.tox-tinymce {
    border: 1px solid #ced4da; /* Example border */
}
</style>