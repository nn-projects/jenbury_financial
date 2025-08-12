<?php
/**
 * Jenbury Financial - Module View Page
 * Adapted to match the structure of Courses/view.php for consistency.
 */
$this->assign('title', h($module->title));

// Assume Font Awesome is included via layout (jenbury.php)
?>

<div class="module-page-main-wrapper">
    <div class="course-header"> <?php // Changed class ?>
        <div class="row">
            <div class="column column-70">
                <?php // Removed Breadcrumbs ?>
                <h1><?= h($module->title) ?></h1>
            </div>
            <div class="column column-30 text-right">
                <?php if ($hasPurchased): ?>
                    <span class="badge badge-success">Purchased</span>
                <?php else: ?>
                    <?php // SWAPPED: Back to Dashboard button now here
                        echo $this->Html->link(
                            '‹ ' . __('Back to Dashboard'),
                            ['controller' => 'Dashboard', 'action' => 'index'],
                            ['class' => 'button button-secondary purchase-button', 'escape' => false, 'style' => 'margin-top: 5px;'] // Added purchase-button for consistency if needed and margin
                        );
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($module->course && !$ownsCourse && !$hasPurchased && isset($savingsPercentage) && $savingsPercentage > 0): ?>
        <div class="row">
            <div class="column">
                <div class="callout callout-info" style="margin-top: 1rem; margin-bottom: 1rem; padding: 0.75rem 1.25rem; border: 1px solid #bee5eb; border-radius: 0.25rem; background-color: #d1ecf1; color: #0c5460;">
                    <p style="margin-bottom: 0;">
                        <strong>Save <?= number_format($savingsPercentage, 0) ?>%</strong> by purchasing the full course '<?= $this->Html->link(h($module->course->title), ['controller' => 'Courses', 'action' => 'view', $module->course->id]) ?>'
                        for comprehensive learning!
                        (Full course price: $<?= number_format($module->course->price, 2) ?>)
                    </p>
                </div>
            </div>
        </div>
    <?php elseif ($module->course && !$ownsCourse && !$hasPurchased): // Fallback message if savingsPercentage is not applicable or zero ?>
        <div class="row">
            <div class="column">
                <div class="callout callout-info" style="margin-top: 1rem; margin-bottom: 1rem; padding: 0.75rem 1.25rem; border: 1px solid #bee5eb; border-radius: 0.25rem; background-color: #d1ecf1; color: #0c5460;">
                    <p style="margin-bottom: 0;">
                        This module is part of the '<?= $this->Html->link(h($module->course->title), ['controller' => 'Courses', 'action' => 'view', $module->course->id]) ?>' course.
                        Consider purchasing the full course for comprehensive learning!
                        (Full course price: $<?= number_format($module->course->price, 2) ?>)
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="module-content-flex-container">
        <div class="module-main-content-area">
            <div class="card"> <!-- Single card for main content -->
                <div class="card-body" style="padding: var(--space-6);"> <!-- Apply padding to this single card-body -->
                    <?php /* Removed course-image section - Modules don't have a main image in this design */ ?>

                    <div class="course-description" style="margin-bottom: 2rem;"> <?php // Added margin for spacing below description ?>
                        <h2>Module Description</h2>
                        <p><?= nl2br(h($module->description)) ?></p>
                    </div>

                    <?php if (!empty($module->contents)): ?>
                        <div id="course-modules" class="course-modules"> <?php // Changed class ?>
                            <h2>Module Contents</h2>
                            <div class="module-list"> <?php // Changed class ?>
                                <?php foreach ($module->contents as $index => $content): ?>
                                    <?php
                                        $contentStatus = $content->user_status ?? 'not_started'; // Status attached in controller
                                        $contentItemClasses = ['module-item']; // Changed class
                                        if ($contentStatus === 'completed') {
                                            $contentItemClasses[] = 'content-completed'; // Add class for styling completed content
                                        }
                                    ?>
                                    <div class="<?= implode(' ', $contentItemClasses) ?>">
                                        <div class="row">
                                            <?php /* Removed icon column */ ?>
                                            <div class="column column-70">
                                                <h3>
                                                    <?php // Add icon based on type before title ?>
                                                    <?php if ($content->type === 'video'): ?>
                                                        <i class="fas fa-video fa-fw" style="margin-right: 5px; color: var(--jf-gold-accent);"></i>
                                                    <?php elseif ($content->type === 'text'): ?>
                                                        <i class="fas fa-file-alt fa-fw" style="margin-right: 5px; color: var(--jf-gold-accent);"></i>
                                                    <?php elseif ($content->type === 'image'): ?>
                                                        <i class="fas fa-image fa-fw" style="margin-right: 5px; color: var(--jf-gold-accent);"></i>
                                                    <?php elseif ($content->type === 'file'): ?>
                                                        <i class="fas fa-file fa-fw" style="margin-right: 5px; color: var(--jf-gold-accent);"></i>
                                                    <?php endif; ?>
                                                    <?= ($index + 1) . '. ' . h($content->title) ?>
                                                    <?php // Add checkmark if completed ?>
                                                    <?php if ($contentStatus === 'completed'): ?>
                                                        <i class="fas fa-check-circle" style="color: var(--jf-green-success); margin-left: 5px;" title="Completed"></i>
                                                    <?php endif; ?>
                                                </h3>
                                                <?php // Display content snippet only if type is text? Or keep generic? Keeping generic for now. ?>
                                                <?php // Content snippet removed as per request ?>
                                            </div>
                                            <div class="column column-30 text-right">
                                                <?php if ($hasPurchased): ?>
                                                    <?php
                                                    // Determine button text and state based on contentStatus
                                                    $buttonText = 'View Content';
                                                    $buttonClasses = ['button', 'button-outline', 'button-small'];
                                                    $isDisabled = false;

                                                    if ($contentStatus === 'completed') {
                                                        $buttonText = 'Review Content'; // Changed text
                                                        // $buttonClasses[] = 'button-completed'; // Removed this class addition
                                                    }
                                                    ?>
                                                    <?= $this->Html->link(
                                                        $buttonText,
                                                        ['controller' => 'Modules', 'action' => 'content', $module->id, $content->id],
                                                        [
                                                            'class' => implode(' ', $buttonClasses),
                                                            'disabled' => $isDisabled // Not disabling viewed content, user might want to review
                                                        ]
                                                    ) ?>
                                                <?php else: ?>
                                                    <?php // Show lock icon instead of button if not purchased ?>
                                                    <span style="font-size: 1.5em; color: var(--jf-grey-text);"><i class="fas fa-lock"></i></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php elseif ($hasPurchased): // Case where module is purchased but has no content ?>
                         <div class="course-modules">
                             <h2>Module Contents</h2>
                             <p>No content has been added to this module yet.</p>
                         </div>
                    <?php else: // Case where module is not purchased and might have content (locked view) ?>
                        <div class="course-modules"> <?php // Changed class ?>
                            <h2>Module Contents</h2>
                            <div class="module-contents locked" style="margin-top: var(--space-4); padding: var(--space-6);"> <?php // Reusing locked style, adjusted padding/margin ?>
                                <div class="locked-content">
                                    <div class="lock-icon" style="font-size: var(--text-3xl);"> <?php // Adjusted size ?>
                                        <i class="fas fa-lock"></i> <?php // Font Awesome icon ?>
                                    </div>
                                    <h3>Content Locked</h3>
                                    <p>Purchase this module to access the content.</p>
                                    <?php // Add to Cart / View Cart button for the module in locked content section
                                    if ($moduleStatus === 'in_cart'):
                                        echo $this->Html->link('View Cart', ['controller' => 'Carts', 'action' => 'view'], ['class' => 'button button-view-cart button-secondary purchase-button']);
                                        // Or: echo '<button class="button button-disabled purchase-button" disabled>Already in Cart</button>';
                                    elseif ($moduleStatus === 'available'):
                                        echo $this->Form->create(null, ['url' => ['controller' => 'Carts', 'action' => 'add']]);
                                        echo $this->Form->hidden('item_id', ['value' => $module->id]);
                                        echo $this->Form->hidden('item_type', ['value' => 'Module']);
                                        echo $this->Form->hidden('quantity', ['value' => 1]);
                                        echo $this->Form->button(__('Add Module to Cart'), ['type' => 'submit', 'class' => 'button btn-primary purchase-button']);
                                        echo $this->Form->end();
                                    endif;
                                    // 'owned' status is handled by the outer if (!$hasPurchased)
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="add-to-cart-module-section" style="margin-top: 20px; text-align: left;">
                        <?php // SWAPPED: Add to Cart / View Cart button for the module now here
                        if (!$hasPurchased): // Only show if not purchased
                            if ($moduleStatus === 'in_cart'):
                                echo $this->Html->link('View Cart', ['controller' => 'Carts', 'action' => 'view'], ['class' => 'button button-view-cart button-secondary']);
                            elseif ($moduleStatus === 'available'):
                                echo $this->Form->create(null, ['url' => ['controller' => 'Carts', 'action' => 'add']]);
                                echo $this->Form->hidden('item_id', ['value' => $module->id]);
                                echo $this->Form->hidden('item_type', ['value' => 'Module']);
                                // echo $this->Form->hidden('item_type', ['value' => 'Module']); // Duplicate line removed by previous diff
                                echo $this->Form->hidden('quantity', ['value' => 1]);
                                echo $this->Form->button(__('Add Module to Cart'), ['type' => 'submit', 'class' => 'button btn-primary']);
                                echo $this->Form->end();
                            endif;
                        endif;
                        ?>
                    </div>
                </div> <!-- End single card-body -->
            </div> <!-- End single card -->
        </div>

        <div class="module-sidebar-area">
            <div class="card">
                <div class="card-header">
                    <h3>Module Details</h3>
                </div>
                <div class="card-body" style="padding: var(--space-6);"> <?php // Added padding directly ?>
                    <ul class="course-details-list"> <?php // Changed class for consistency ?>
                        <li>
                            <strong>Part of Course:</strong> <?= $this->Html->link(h($module->course->title), ['controller' => 'Courses', 'action' => 'view', $module->course->id]) ?>
                        </li>
                        <li>
                            <strong>Contents:</strong> <?= count($module->contents) ?> items
                        </li>
                        <li>
                            <strong>Price:</strong> $<?= number_format((float)$module->price, 2) ?>
                        </li>
                        <li>
                            <strong>Last Updated:</strong> <?= $module->modified->format('F j, Y') ?>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>What You'll Learn</h3>
                </div>
                <div class="card-body" style="padding: var(--space-6);"> <?php // Added padding directly ?>
                    <ul class="learning-outcomes">
                        <?php // Use module-specific learning outcomes if available, otherwise use placeholders ?>
                        <?php if (!empty($module->learning_outcomes)): ?>
                            <?php foreach (explode("\n", h($module->learning_outcomes)) as $outcome): // Example if outcomes are stored as newline-separated string ?>
                                <?php if(trim($outcome)): ?>
                                    <li><?= trim($outcome) ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Understand key concepts related to this module</li>
                            <li>Apply practical strategies</li>
                            <li>Identify opportunities for improvement</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <?php // Added "This Module Includes" card ?>
            <div class="card">
                <div class="card-header">
                    <h3>This Module Includes</h3>
                </div>
                <div class="card-body" style="padding: var(--space-6);"> <?php // Added padding directly ?>
                    <ul class="course-includes"> <?php // Changed class for consistency ?>
                        <?php // Dynamically list included types based on content? Or fixed list? Fixed for now. ?>
                        <li><i class="fas fa-video fa-fw"></i> Video lessons</li>
                        <li><i class="fas fa-file-alt fa-fw"></i> Text content</li>
                        <li><i class="fas fa-file fa-fw"></i> Downloadable files</li>
                        <li><i class="fas fa-infinity fa-fw"></i> Lifetime access</li> <?php // Example ?>
                    </ul>
                </div>
            </div>

            <?php if (!$hasPurchased): ?>
                <div class="card">
                    <div class="card-header">
                        <h3>Purchase Options</h3>
                    </div>
                    <div class="card-body" style="padding: var(--space-6);"> <?php // Added padding directly ?>
                        <div class="purchase-option">
                            <h4>This Module</h4>
                            <p>Get access to this module only</p>
                            <div class="option-price">$<?= number_format($module->price, 2) ?></div>
                            <?php // Add to Cart / View Cart button for the module in sidebar
                            if ($moduleStatus === 'in_cart'):
                                echo $this->Html->link('View Cart', ['controller' => 'Carts', 'action' => 'view'], ['class' => 'button button-view-cart button-secondary purchase-button']);
                                // Or: echo '<button class="button button-disabled purchase-button" disabled>Already in Cart</button>';
                            elseif ($moduleStatus === 'available'):
                                echo $this->Form->create(null, ['url' => ['controller' => 'Carts', 'action' => 'add']]);
                                echo $this->Form->hidden('item_id', ['value' => $module->id]);
                                echo $this->Form->hidden('item_type', ['value' => 'Module']);
                                echo $this->Form->hidden('quantity', ['value' => 1]);
                                echo $this->Form->button(__('Add Module to Cart'), ['type' => 'submit', 'class' => 'button btn-primary purchase-button']);
                                echo $this->Form->end();
                            endif;
                            // 'owned' status is handled by the outer if (!$hasPurchased)
                            ?>
                        </div>

                        <?php // Option to buy the full course ?>
                        <div class="purchase-option">
                            <h4>Full Course</h4>
                            <p>Get access to all modules in '<?= h($module->course->title) ?>'</p>
                            <div class="option-price">$<?= number_format($module->course->price, 2) ?></div>
                            <?php // Add to Cart button for the full course in sidebar ?>
                            <?= $this->Form->create(null, ['url' => ['controller' => 'Carts', 'action' => 'add']]) ?>
                                <?= $this->Form->hidden('item_id', ['value' => $module->course->id]) ?>
                                <?= $this->Form->hidden('item_type', ['value' => 'Course']) ?>
                                <?= $this->Form->hidden('quantity', ['value' => 1]) ?>
                                <?= $this->Form->button(__('Add Full Course to Cart'), ['type' => 'submit', 'class' => 'button btn-primary purchase-button']) ?>
                            <?= $this->Form->end() ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php // Navigation section - adapted from course view ?>
    <?php if ($hasPurchased && !empty($module->contents)): ?>
        <div class="course-navigation"> <?php // Changed class ?>
            <h2>Module Navigation</h2> <?php // Changed title ?>

            <?php // TODO: Add progress tracking logic for module content if needed ?>
            <div class="overall-progress-container" style="margin-bottom: var(--space-6);">
                 <h4>Module Progress</h4>
                 <div class="progress-container" style="height: 1.5rem;">
                     <?php $modulePercent = $moduleProgressData['modulePercentage'] ?? 0; ?>
                     <div id="module-progress-bar" class="progress-bar" style="width: <?= $modulePercent ?>%;">
                         <span id="module-progress-text" class="progress-text" style="font-size: 0.9rem;"><?= $modulePercent ?>% Complete</span>
                     </div>
                 </div>
            </div>

            <div class="course-navigation-modules-scrollable">
                <div class="row">
                <?php foreach ($module->contents as $index => $content): ?>
                    <div class="column column-25">
                        <div class="nav-module-item"> <?php // Changed class ?>
                             <div class="content-icon" style="font-size: var(--text-xl); margin-bottom: var(--space-2);"> <?php // Added icon div ?>
                                <?php if ($content->type === 'video'): ?>
                                    <i class="fas fa-video fa-fw"></i>
                                <?php elseif ($content->type === 'text'): ?>
                                    <i class="fas fa-file-alt fa-fw"></i>
                                <?php elseif ($content->type === 'image'): ?>
                                    <i class="fas fa-image fa-fw"></i>
                                <?php elseif ($content->type === 'file'): ?>
                                    <i class="fas fa-file fa-fw"></i>
                                <?php endif; ?>
                            </div>
                            <h3><?= ($index + 1) . '. ' . h($content->title) ?></h3>
                            <?php
                            // Determine button text and state based on progress (placeholder)
                            $buttonTextNav = 'View Content';
                            $buttonClassesNav = ['button', 'button-outline', 'button-small'];
                            $isDisabledNav = false;
                            // $contentStatusNav = $content->user_status ?? 'not_viewed';
                            // if ($contentStatusNav === 'viewed') { ... }
                            ?>
                            <?= $this->Html->link(
                                $buttonTextNav,
                                ['controller' => 'Modules', 'action' => 'content', $module->id, $content->id],
                                [
                                    'class' => implode(' ', $buttonClassesNav),
                                    'disabled' => $isDisabledNav
                                    // Add data attributes if needed for JS progress tracking
                                ]
                            ) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php // Removed Related Modules section ?>
</div>