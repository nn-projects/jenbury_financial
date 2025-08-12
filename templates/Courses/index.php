<!-- filepath: c:\xampp\htdocs\team109-app_fit3048\templates\Courses\index.php -->
<?php
/**
 * Jenbury Financial - Courses Index Page (Re-engineered)
 *
 * Displays courses and their modules based on the new design.
 */
$this->assign('title', 'Financial Education Courses');

// Helper function to generate CSS classes (optional, but can be useful)
$cssClasses = function (...$args) {
    return implode(' ', array_filter($args));
};
?>

<div class="courses-page">

    <!-- Page Header -->
    <div class="courses-page-header">
        <h1 class="page-title">Financial Education Courses</h1>
        <p class="page-description">
            Explore our comprehensive range of financial education courses designed to help
            you achieve your financial goals. Purchase individual modules or save with our full
            course bundles.
        </p>
    </div>

    <?php if (!empty($courses)): ?>
        <?php foreach ($courses as $course): ?>
            <section class="course-section">
                <!-- Course Header Component (Two-column layout) -->
                <div class="course-header-component">
                    <div class="course-info-column">
                        <h2 class="course-title">
                            <?php if ($course->title === 'The Jenbury Method for up-and-coming financial planners'): ?>
                                <?= h($course->title) ?>
                            <?php else: ?>
                                <?= $this->Html->link(h($course->title), ['controller' => 'Courses', 'action' => 'view', $course->id], ['class' => 'course-title-link']) ?>
                            <?php endif; ?>
                        </h2>
                        <p class="course-description-main"><?= h($course->description) ?></p>

                        <?php if (!empty($course->learning_outcomes)): ?>
                            <h3 class="learning-outcomes-title"><strong>Learning Outcomes</strong></h3>
                            <ul class="learning-outcomes-list">
                                <?php foreach ($course->learning_outcomes as $outcome): ?>
                                    <li><span class="checkmark-icon">✓</span> <?= h($outcome) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($course->image)): ?>
                            <?= $this->Html->image(str_replace('\\', '/', $course->image), ['style' => 'width: 85%; max-width: 900px; max-height: 475px;']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="course-bundle-column">
                        <!-- Course Bundle Card -->
                        <div class="course-bundle-card">
                            <h3 class="bundle-title">Full Course Bundle</h3>
                            <p class="bundle-savings">Get all <?= count($course->modules) ?> modules and save <?= h($course->bundle_discount_percent ?? 'X') ?>%</p>
                            <p class="bundle-individual-price">Individual Modules: <span class="strikethrough">$<?= number_format($course->total_module_price ?? 0, 2) ?></span></p>
                            <p class="bundle-price">Bundle Price: <strong>$<?= number_format($course->price, 2) ?></strong></p>
                            <p class="bundle-savings-amount">Save $<?= number_format(($course->total_module_price ?? 0) - $course->price, 2) ?></p>
                            <?php
                            $courseStatus = $itemStatuses['Course'][$course->id] ?? 'available'; // Default if not set
                            if ($courseStatus === 'owned'):
                                echo $this->Html->link('View Course', ['controller' => 'Courses', 'action' => 'view', $course->id], ['class' => 'button button-view button-secondary']);
                            elseif ($courseStatus === 'in_cart'):
                                echo '<div style="text-align: center;">';
                                echo $this->Html->link('View Cart', ['controller' => 'Carts', 'action' => 'view'], ['class' => 'button button-view-cart button-secondary button-extra-padding-x']);
                                echo '</div>';
                                // Or: echo '<button class="button button-disabled" disabled>Already in Cart</button>';
                            else: // 'available'
                                if (count($course->modules) === 0):
                                    echo $this->Form->button(__('Coming Soon'), ['type' => 'button', 'class' => 'button button-disabled', 'disabled' => true]);
                                else:
                                    echo $this->Form->create(null, ['url' => ['controller' => 'Carts', 'action' => 'add']]);
                                    echo $this->Form->hidden('item_id', ['value' => $course->id]);
                                    echo $this->Form->hidden('item_type', ['value' => 'Course']);
                                    echo $this->Form->hidden('quantity', ['value' => 1]); // Quantity is always 1
                                    echo $this->Form->button(__('Add Full Course to Cart'), ['type' => 'submit', 'class' => 'button button-add-cart button-gold']);
                                    echo $this->Form->end();
                                endif;
                            endif;
                            ?>
                            <p class="bundle-browse-modules-link">
                                <?= $this->Html->link('Or browse individual modules below', '#course-' . $course->id . '-modules') ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Available Modules Section -->
                <div class="available-modules-section" id="course-<?= $course->id ?>-modules">
                    <h2 class="available-modules-title">Available Modules</h2>
                    <?php if (!empty($course->modules)): ?>
                        <div class="modules-grid">
                            <?php foreach ($course->modules as $module): ?>
                                <!-- Module Card Component -->
                                <div class="module-card">
                                    <div class="module-card-image">
                                        <?php if (!empty($module->image_url)): ?>
                                            <?= $this->Html->image($module->image_url, [
                                                'alt' => h($module->title), // Alt text should still be escaped
                                                'class' => 'module-actual-image',
                                                'style' => 'width: 100%; height: 160px; object-fit: cover;'
                                            ]) ?>
                                        <?php else: ?>
                                            <div class="module-image-placeholder" style="width: 100%; height: 160px; background-color: #eee; display: flex; align-items: center; justify-content: center; text-align: center; border: 1px solid #ccc; box-sizing: border-box;">
                                                <span style="color: #888; font-size: 1.1em; padding: 10px;"><?= $module->title ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="module-card-content">
                                        <h3 class="module-title">
                                            <?= $this->Html->link($module->title, ['controller' => 'Modules', 'action' => 'view', $module->id], ['class' => 'module-title-link', 'escapeTitle' => false]) ?>
                                        </h3>
                                        <p class="module-description"><?= $this->Text->truncate(h($module->description), 100, ['ellipsis' => '...', 'exact' => false]) ?></p>
                                        <p class="module-lesson-count">
                                            <?= h($module->lesson_count ?? 'X') ?> Lessons
                                        </p>
                                    </div>
                                    <div class="module-card-footer">
                                        <span class="module-price">$<?= number_format($module->price, 2) ?></span>
                                        <?php
                                        $moduleStatus = $itemStatuses['Module'][$module->id] ?? 'available'; // Default if not set
                                        if ($moduleStatus === 'owned'):
                                            echo $this->Html->link('View Module', ['controller' => 'Modules', 'action' => 'view', $module->id], ['class' => 'button button-view button-gold']);
                                        elseif ($moduleStatus === 'in_cart'):
                                            // Option 1: Link to cart (using this one as it's consistent with course bundle 'in_cart' behavior)
                                            echo $this->Html->link('View Cart', ['controller' => 'Carts', 'action' => 'view'], ['class' => 'button button-view-cart button-secondary']);
                                            // Option 2: Disabled button
                                            // echo '<button class="button button-disabled" disabled>In Cart</button>';
                                        else: // 'available'
                                            echo $this->Form->create(null, ['url' => ['controller' => 'Carts', 'action' => 'add']]);
                                            echo $this->Form->hidden('item_id', ['value' => $module->id]);
                                            echo $this->Form->hidden('item_type', ['value' => 'Module']);
                                            echo $this->Form->hidden('quantity', ['value' => 1]); // Default quantity
                                            echo $this->Form->button(__('Add to Cart'), ['type' => 'submit', 'class' => 'button button-add-cart button-purple']);
                                            echo $this->Form->end();
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No modules available for this course yet.</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-courses">
            <p>No courses are currently available. Please check back later.</p>
        </div>
    <?php endif; ?>

</div>