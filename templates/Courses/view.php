<!-- filepath: c:\xampp\htdocs\team109-app_fit3048\templates\Courses\view.php -->
<?php
/**
 * Jenbury Financial - Course View Page
 */
$this->assign('title', h($course->title));
?>

<div class="course-header">
    <div class="row">
        <div class="column column-70">
            <h1><?= h($course->title) ?></h1>
        </div>
        <div class="column column-30 text-right">
            <?php if ($hasPurchased): ?>
                <?php
                $actionButtonText = 'Start Course';
                $actionButtonUrl = $courseProgressData['firstUncompletedContentUrl'] ?? '#';
                $actionButtonClasses = ['button', 'button-accent', 'button-lg'];

                if ($courseProgressData['courseStatus'] === 'in_progress') {
                    $actionButtonText = 'Continue Learning';
                    $actionButtonUrl = $courseProgressData['lastAccessedContentUrl'] ?? $courseProgressData['firstUncompletedContentUrl'] ?? '#';
                } elseif ($courseProgressData['courseStatus'] === 'completed') {
                    $actionButtonText = 'Course Completed';
                    $actionButtonClasses[] = 'button-disabled';
                    $actionButtonUrl = '#';
                }
                ?>
                <?= $this->Html->link($actionButtonText, $actionButtonUrl, ['class' => implode(' ', $actionButtonClasses)]) ?>
            <?php else: // User hasn't purchased ?>
                <div class="course-price">$<?= number_format($course->price, 2) ?></div>
                <?php // Add to Cart / View Cart button for the full course
                if ($courseStatus === 'in_cart'):
                    echo $this->Html->link('View Cart', ['controller' => 'Carts', 'action' => 'view'], ['class' => 'button button-view-cart button-secondary']);
                    // Or: echo '<button class="button button-disabled" disabled>Already in Cart</button>';
                elseif ($courseStatus === 'available'):
                endif;
                // 'owned' status is handled by the outer if ($hasPurchased)
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row course-view-container">
    <div class="column column-70 course-view-main">
        <div class="card">
            <div class="card-body">
                <div class="course-image">
                    <?php if (!empty($course->image)): ?>
                        <?= $this->Html->image($course->image, [
                            'alt' => h($course->title),
                            'style' => 'width: 100%; height: auto; max-height: 400px; object-fit: cover; display: block; border-radius: var(--rounded-md);'
                        ]) ?>
                    <?php else: ?>
                        <div class="module-image-placeholder" style="width: 100%; height: 250px; background-color: #eee; display: flex; align-items: center; justify-content: center; text-align: center; border: 1px solid #ccc; box-sizing: border-box; border-radius: var(--rounded-md);">
                            <span style="color: #888; font-size: 1.4em; padding: 10px;"><?= h($course->title) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="course-description">
                    <h2>Course Description</h2>
                    <p><?= nl2br(h($course->description)) ?></p>
                </div>

                <?php if (!empty($course->modules)): ?>
                    <div id="course-modules" class="course-modules">
                        <h2>Course Modules</h2>
                        <div class="module-list">
                            <?php foreach ($course->modules as $index => $module): ?>
                                <?php
                                $moduleStatus = $module->user_status ?? 'not_started';
                                $modulePercentage = $courseProgressData['modulePercentages'][$module->id] ?? 0;
                                $moduleItemClasses = ['module-item'];
                                if ($moduleStatus === 'completed') {
                                    $moduleItemClasses[] = 'module-completed';
                                }
                                ?>
                                <div class="<?= implode(' ', $moduleItemClasses) ?>">
                                    <div class="row">
                                        <div class="column column-70">
                                            <h3>
                                                <?= $this->Html->link(
                                                    ($index + 1) . '. ' . h($module->title),
                                                    ['controller' => 'Modules', 'action' => 'view', $module->id],
                                                    ['class' => 'module-title-link']
                                                ) ?>
                                                <?php if ($moduleStatus === 'completed'): ?>
                                                    <i class="fas fa-check-circle" style="color: var(--jf-green-success); margin-left: 5px;" title="Module Completed"></i>
                                                <?php endif; ?>
                                            </h3>
                                            <p><?= $this->Text->truncate(h($module->description), 150, ['ellipsis' => '...', 'exact' => false]) ?></p>

                                            <?php if ($hasPurchased): ?>
                                                <div class="progress-container progress-container-small" style="margin-top: var(--space-2);">
                                                    <div class="progress-bar" style="width: <?= $modulePercentage ?>%;">
                                                        <span class="progress-text progress-text-small"><?= $modulePercentage ?>%</span>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="column column-30 text-right">
                                            <?php if ($hasPurchased): ?>
                                                <?php
                                                $buttonText = 'Start Module';
                                                $buttonClasses = ['button', 'button-small', 'module-action-button'];

                                                if ($moduleStatus === 'in_progress') {
                                                    $buttonText = 'Continue Learning';
                                                    $buttonClasses[] = 'button-accent';
                                                } elseif ($moduleStatus === 'completed') {
                                                    $buttonText = 'Review Module';
                                                    $buttonClasses[] = 'button-outline';
                                                } else {
                                                    $buttonText = 'Start Module';
                                                    $buttonClasses[] = 'button-accent';
                                                }
                                                ?>
                                                <?= $this->Html->link(
                                                    $buttonText,
                                                    ['controller' => 'Modules', 'action' => 'view', $module->id],
                                                    ['class' => implode(' ', $buttonClasses)]
                                                ) ?>
                                            <?php else: // User hasn't purchased the *course*, check individual module ownership ?>
                                                <div class="module-price">$<?= number_format($module->price, 2) ?></div>
                                                <?php
                                                // This logic assumes the controller provides an $individualModuleItemStatuses array
                                                // where $individualModuleItemStatuses[$module->id] can be 'owned', 'in_cart', or 'available'.
                                                // This mirrors the $itemStatuses['Module'] structure from Courses/index.php.
                                                $currentIndividualModuleStatus = $individualModuleItemStatuses[$module->id] ?? 'available';

                                                if ($currentIndividualModuleStatus === 'owned') {
                                                    echo $this->Html->link('View Module', ['controller' => 'Modules', 'action' => 'view', $module->id], ['class' => 'button button-view button-gold button-small']);
                                                } elseif ($currentIndividualModuleStatus === 'in_cart') {
                                                    echo $this->Html->link('View Cart', ['controller' => 'Carts', 'action' => 'view'], ['class' => 'button button-view-cart button-secondary button-small']);
                                                } else { // 'available'
                                                    echo $this->Form->create(null, ['url' => ['controller' => 'Carts', 'action' => 'add']]);
                                                    echo $this->Form->hidden('item_id', ['value' => $module->id]);
                                                    echo $this->Form->hidden('item_type', ['value' => 'Module']);
                                                    echo $this->Form->hidden('quantity', ['value' => 1]);
                                                    echo $this->Form->button(__('Add Module to Cart'), ['type' => 'submit', 'class' => 'button button-add-cart button-accent button-small']);
                                                    echo $this->Form->end();
                                                }
                                                ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="column column-30 course-view-sidebar">
        <div class="card">
            <div class="card-header">
                <h3>Course Details</h3>
            </div>
            <div class="card-body">
                <ul class="course-details-list">
                    <li><strong>Modules:</strong> <?= count($course->modules) ?></li>
                    <li><strong>Total Duration:</strong> Approximately 10 hours</li>
                    <li><strong>Level:</strong> Beginner to Intermediate</li>
                    <li><strong>Last Updated:</strong> <?= $course->modified->format('F j, Y') ?></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>What You'll Learn</h3>
            </div>
            <div class="card-body">
                <ul class="learning-outcomes">
                    <li>Understand the fundamentals of financial planning</li>
                    <li>Create a personalized budget that works for your lifestyle</li>
                    <li>Learn strategies for debt reduction and management</li>
                    <li>Develop a savings plan for short and long-term goals</li>
                    <li>Understand basic investment principles and options</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>This Course Includes</h3>
            </div>
            <div class="card-body">
                <ul class="course-includes">
                    <li><i class="icon-video"></i> Video lessons</li>
                    <li><i class="icon-document"></i> Downloadable resources</li>
                    <li><i class="icon-quiz"></i> Quizzes and assessments</li>
                    <li><i class="icon-certificate"></i> Completion certificate</li>
                    <li><i class="icon-lifetime"></i> Lifetime access</li>
                </ul>
            </div>
        </div>

        <?php if (!$hasPurchased): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Purchase Options</h3>
                </div>
                <div class="card-body">
                    <div class="purchase-option">
                        <h4>Full Course</h4>
                        <p>Get access to all modules and materials</p>
                        <div class="option-price">$<?= number_format($course->price, 2) ?></div>
                        <?php // Add to Cart / View Cart button for the full course in sidebar
                        if ($courseStatus === 'in_cart'):
                            echo $this->Html->link('View Cart', ['controller' => 'Carts', 'action' => 'view'], ['class' => 'button button-view-cart button-secondary purchase-button']);
                            // Or: echo '<button class="button button-disabled purchase-button" disabled>Already in Cart</button>';
                        elseif ($courseStatus === 'available'):
                            echo $this->Form->create(null, ['url' => ['controller' => 'Carts', 'action' => 'add']]);
                            echo $this->Form->hidden('item_id', ['value' => $course->id]);
                            echo $this->Form->hidden('item_type', ['value' => 'Course']);
                            echo $this->Form->hidden('quantity', ['value' => 1]);
                            echo $this->Form->button(__('Add Full Course to Cart'), ['type' => 'submit', 'class' => 'button button-add-cart button-gold purchase-button']);
                            echo $this->Form->end();
                        endif;
                        // 'owned' status is handled by the outer if (!$hasPurchased)
                        ?>
                    </div>

                    <div class="purchase-option">
                        <h4>Individual Modules</h4>
                        <p>Purchase only the modules you need</p>
                        <p>Starting at $<?= number_format(min(array_column($course->modules, 'price')), 2) ?></p>
                        <a href="#course-modules" class="button button-outline">View Modules</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->Html->script('smooth-scroll.js') ?>
<?= $this->Html->script('course-progress.js', ['block' => true]) ?>