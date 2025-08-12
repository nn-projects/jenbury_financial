<?php
$this->assign('title', 'Add Course');
?>
<?= $this->Html->css('admin/add-course', ['block' => true]) ?>

<div class="add-course-page">

<header class="admin-page-header">
    <h1><?= __('Add New Course') ?></h1>
    <div class="admin-back-link">
        <?= $this->Html->link(
            '‹ ' . __('Back to Dashboard'),
            ['action' => 'dashboard'],
            ['class' => 'button button-primary', 'escape' => false] // Matched class from discount codes, added escape false for icon
        ) ?>
    </div>
</header>

<div class="multi-step-form-container">
    <?= $this->Form->create($course, ['type' => 'file', 'id' => 'course-form', 'class' => 'needs-validation', 'novalidate' => true]) ?>

    <!-- Step 1 -->
    <div class="form-step active">
        <h2 class="step-heading">Course Details</h2>
        <p class="step-subheading">Tell us about your course.</p>

        <div class="input"> <?php // Changed class ?>
            <?= $this->Form->control('title', [
                'label' => 'Course Title',
                'placeholder' => 'Give your course a name',
                'required' => true,
                'maxlength' => 150,
                'class' => 'form-input'
            ]) ?>
            <?php // Removed validation-message div ?>
        </div>

        <div class="input"> <?php // Changed class ?>
            <?= $this->Form->control('description', [
                'type' => 'textarea',
                'label' => 'Course Description',
                'placeholder' => 'What is this course about?',
                'required' => true,
                'maxlength' => 600,
                'class' => 'form-input large-textarea'
            ]) ?>
            <?php // Removed validation-message div ?>
        </div>

        <button type="button" class="button button-primary next-step">Continue</button>
    </div>

    <!-- Step 2 -->
    <div class="form-step">
        <h2 class="step-heading">Pricing & Thumbnail</h2>

        <div class="input"> <?php // Changed class ?>
            <?= $this->Form->control('price', [
                'type' => 'text', // Changed type to text for maxlength to work
                'inputmode' => 'decimal', // Hint for mobile keyboards
                'id' => 'price-input',
                // 'pattern' => '^\d{1,6}(?:[.]\d{1,2})?$', // Pattern for text input (adjust precision as needed) - Can be added if specific format needed beyond just digits/dot
                'maxlength' => 8, // Maxlength now works with type="text" (e.g., 12345.78 = 8 chars)
                'label' => 'Price (AUD)',
                'placeholder' => 'e.g., 49.99', // Updated placeholder
                'required' => true,
                'class' => 'form-input'
            ]) ?>
            <?php // Removed validation-message div ?>
        </div>

        <div class="input"> <?php // Changed class ?>
            <?= $this->Form->control('image_file', [
                'type' => 'file',
                'label' => 'Course Thumbnail',
                'accept' => 'image/*',
                'class' => 'form-input'
            ]) ?>
        </div>

        <div class="step-actions">
            <button type="button" class="button button-primary prev-step">Back</button>
            <button type="button" class="button button-primary next-step">Next</button>
        </div>
    </div>

    <!-- Step 3 -->
    <div class="form-step">
        <h2 class="step-heading">Publish Course</h2>

        <div class="form-group form-toggle">
            <?= $this->Form->control('is_active', [
                'type' => 'checkbox',
                'label' => 'Make this course visible to students',
                'checked' => true,
                'class' => 'toggle-checkbox'
            ]) ?>
        </div>

        <div class="step-actions">
            <button type="button" class="button button-primary prev-step">Back</button>
            <?= $this->Form->button('Save Course', ['class' => 'button button-primary']) ?>
        </div>  
</div>




<?php /*
// 🔍 Check if form has validation errors
if (!empty($course->getErrors())) {
    echo '<pre>';
    echo '⚠️ Validation Errors:' . "\n";
    print_r($course->getErrors());
    echo '</pre>';
}

// 🔍 Check posted data
if ($this->request->is('post')) {
    echo '<pre>';
    echo '📦 POST DATA:' . "\n";
    print_r($this->request->getData());
    echo '</pre>';
}
*/?>



        <?= $this->Form->end() ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const steps = document.querySelectorAll(".form-step");
    let currentStep = 0;

    const showStep = (i) => {
        steps.forEach((step, index) => {
            step.classList.toggle("active", index === i);
        });
    };

    const validateStep = (i) => {
    const inputs = steps[i].querySelectorAll("input[required], textarea[required]");
    let valid = true;

    // Clear old errors
    steps[i].querySelectorAll(".form-error").forEach(e => e.remove());

    inputs.forEach(input => {
        input.classList.remove("input-error");

        if (!input.value.trim()) {
            input.classList.add("input-error");
            valid = false;

            const error = document.createElement("div");
            error.classList.add("form-error");
            error.textContent = "This field cannot be empty.";
            input.parentElement.appendChild(error);
        }
    });

    return valid;
};


    document.querySelectorAll(".next-step").forEach(btn => {
        btn.addEventListener("click", () => {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        });
    });

    document.querySelectorAll(".prev-step").forEach(btn => {
        btn.addEventListener("click", () => {
            currentStep--;
            showStep(currentStep);
        });
    });

    showStep(currentStep);
});
</script>

<script>
    const priceInput = document.getElementById('price-input');

    priceInput.addEventListener('input', function(e) {
        let value = this.value;
        const originalCursorStart = this.selectionStart;
        const originalCursorEnd = this.selectionEnd;

        // 1. Filter: Allow only digits and one decimal point. Remove others.
        let filteredValue = value.replace(/[^0-9.]/g, '');
        const firstDotIndex = filteredValue.indexOf('.');
        if (firstDotIndex !== -1) {
            filteredValue = filteredValue.substring(0, firstDotIndex + 1) + filteredValue.substring(firstDotIndex + 1).replace(/\./g, '');
        }

        // 2. Limit decimal places to 2
        if (filteredValue.includes('.')) {
            let [integerPart, decimalPart] = filteredValue.split('.');
            if (decimalPart && decimalPart.length > 2) {
                decimalPart = decimalPart.slice(0, 2);
                filteredValue = integerPart + '.' + decimalPart;
            }
        }
        
        // 3. Update value if changed and restore cursor position
        if (this.value !== filteredValue) {
            const lengthDiff = filteredValue.length - value.length;
            this.value = filteredValue;
            // Adjust cursor position based on changes
            const newCursorPos = originalCursorStart + lengthDiff;
            this.setSelectionRange(newCursorPos, newCursorPos);
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

