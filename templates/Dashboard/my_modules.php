<?php
/**
 * Jenbury Financial - My Modules Page
 * Refactored to use consistent card styling from courses.css
 */
$this->assign('title', 'My Modules');
?>

<div class="page-header">
    <h1>My Modules</h1>
</div>

<div class="row dashboard-grid"> <?php // Added dashboard-grid class ?>
    <div class="dashboard-main-content"> <?php // Use a semantic class instead of framework column ?>
        <?php if (!empty($modulePurchases)): ?>
            <div class="card"> <?php // Outer card for the section ?>
                <div class="card-header">
                    <h2>Your Purchased Modules</h2>
                </div>
                <div class="card-content"> <?php // Changed from card-body ?>
                    <div class="module-grid"> <?php // Changed from row, added module-grid ?>
                        <?php foreach ($modulePurchases as $purchase): ?>
                            <div class="column"> <?php // Removed column-50, grid handles sizing ?>
                                <div class="card module-card"> <?php // Changed to standard module-card structure ?>
                                    <?php // Modules typically don't have images, so omitting module-card-image ?>
                                    <div class="module-card-content"> <?php // Added content wrapper ?>
                                        <h3 class="module-title"><?= h($purchase->module->title) ?></h3> <?php // Added class ?>
                                        <p class="course-name" style="font-size: 1.4rem; color: var(--muted-text); margin-bottom: var(--space-md);"> <?php // Added style for consistency ?>
                                            From: <?= $this->Html->link(h($purchase->module->course->title), ['controller' => 'Courses', 'action' => 'view', $purchase->module->course->id]) ?>
                                        </p>
                                        <p class="module-description"><?= $this->Text->truncate(h($purchase->module->description), 100, ['ellipsis' => '...', 'exact' => false]) ?></p> <?php // Added class ?>

                                        <?php if (!empty($purchase->module->contents)): ?>
                                            <div class="module-lesson-count" style="margin-bottom: var(--space-2); color: var(--muted-text); font-size: 1.4rem;"> <?php // Added class and style ?>
                                                 <i class="fas fa-list-ul fa-fw"></i> <?= count($purchase->module->contents) ?> Contents
                                            </div>
                                        <?php endif; ?>

                                        <?php // TODO: Replace with actual module progress calculation ?>
                                        <?php $moduleProgress = 40; // Placeholder ?>
                                        <div class="progress-container" style="height: 1rem; margin-bottom: var(--space-4);"> <?php // Use standard progress structure ?>
                                            <div class="progress-bar" style="width: <?= $moduleProgress ?>%;">
                                                <span class="progress-text" style="font-size: 0.8rem;"><?= $moduleProgress ?>% Complete</span>
                                            </div>
                                        </div>
                                        <?php // Removed module-actions div, button goes in footer ?>
                                    </div>
                                    <div class="module-card-footer"> <?php // Added footer wrapper ?>
                                        <?php // Removed price display ?>
                                        <?= $this->Html->link('Continue Learning', ['controller' => 'Modules', 'action' => 'view', $purchase->module->id], ['class' => 'button button-purple button-enroll']) // Use consistent button classes ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php // Keep pagination if needed ?>
                    <div class="pagination-container" style="margin-top: var(--spacing-lg);">
                        <ul class="pagination">
                            <?= $this->Paginator->prev('« Previous') ?>
                            <?= $this->Paginator->numbers() ?>
                            <?= $this->Paginator->next('Next »') ?>
                        </ul>
                        <p><?= $this->Paginator->counter('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total') ?></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-content empty-state"> <?php // Changed to card-content, added empty-state ?>
                    <h2>You haven't purchased any individual modules yet.</h2>
                    <p>You can purchase individual modules from our courses or buy complete courses.</p>
                    <?= $this->Html->link('Browse Courses', ['controller' => 'Courses', 'action' => 'index'], ['class' => 'button primary-button']) // Use consistent button class ?>
                </div>
            </div>
        <?php endif; ?>

        <?php /* Removed Recommended Modules Section */ ?>
    </div>

    <div class="dashboard-sidebar-content"> <?php // Sidebar column - use semantic class ?>
        <div class="card quick-links"> <?php // Added class ?>
            <div class="card-header">
                <h3>Quick Links</h3>
            </div>
            <div class="card-content"> <?php // Changed from card-body ?>
                <ul class="nav-list">
                    <li><?= $this->Html->link('Dashboard', ['controller' => 'Dashboard', 'action' => 'index']) ?></li>
                    <li><?= $this->Html->link('My Courses', ['controller' => 'Dashboard', 'action' => 'myCourses']) ?></li>
                    <li class="active"><?= $this->Html->link('My Modules', ['controller' => 'Dashboard', 'action' => 'myModules']) ?></li>
                    <li><?= $this->Html->link('Purchase History', ['controller' => 'Dashboard', 'action' => 'purchaseHistory']) ?></li>
                    <li><?= $this->Html->link('My Profile', ['controller' => 'Users', 'action' => 'profile']) ?></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Module vs. Course</h3>
            </div>
             <div class="card-content"> <?php // Changed from card-body ?>
                <div class="comparison-item" style="margin-bottom: var(--spacing-md);"> <?php // Added style ?>
                    <h4>Individual Modules</h4>
                    <ul style="list-style: disc; padding-left: 20px;"> <?php // Added style ?>
                        <li>Focus on specific topics</li>
                        <li>Lower cost per module</li>
                        <li>Learn exactly what you need</li>
                        <li>Shorter time commitment</li>
                    </ul>
                </div>
                <div class="comparison-item"> <?php // Removed margin ?>
                    <h4>Complete Courses</h4>
                     <ul style="list-style: disc; padding-left: 20px;"> <?php // Added style ?>
                        <li>Comprehensive coverage</li>
                        <li>Better value overall</li>
                        <li>Structured learning path</li>
                        <li>More in-depth understanding</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Need Help?</h3>
            </div>
            <div class="card-content"> <?php // Changed from card-body ?>
                <p>If you have any questions about your modules or need assistance, our support team is here to help.</p>
                <?= $this->Html->link('Contact Support', ['controller' => 'Pages', 'action' => 'contact'], ['class' => 'button outline-button']) // Use consistent button class ?>
            </div>
        </div>
    </div>
</div>