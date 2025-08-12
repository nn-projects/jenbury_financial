<?php
/**
 * Jenbury Financial - My Courses Page
 * Refactored to use consistent card styling from courses.css
 */
$this->assign('title', 'My Courses');
?>

<div class="page-header">
    <h1>My Courses</h1>
</div>

<div class="row dashboard-grid"> <?php // Added dashboard-grid class ?>
    <div class="dashboard-main-content"> <?php // Use a semantic class instead of framework column ?>
        <?php if (!empty($coursePurchases)): ?>
            <div class="card"> <?php // Outer card for the section ?>
                <div class="card-header">
                    <h2>Your Purchased Courses</h2>
                </div>
                <div class="card-content"> <?php // Changed from card-body ?>
                    <div class="course-grid"> <?php // Changed from row, added course-grid ?>
                        <?php foreach ($coursePurchases as $purchase): ?>
                            <div class="column"> <?php // Removed column-50, grid handles sizing ?>
                                <div class="card module-card"> <?php // Changed to standard module-card structure ?>
                                    <div class="module-card-image"> <?php // Added image wrapper ?>
                                        <?php // Use placeholder logic similar to index.php module images ?>
                                        <?php if (!empty($purchase->course->image)): ?>
                                            <?= $this->Html->image($purchase->course->image, [
                                                'alt' => h($purchase->course->title),
                                                'style' => 'width: 100%; height: 160px; object-fit: cover; display: block;' // Consistent height
                                            ]) ?>
                                        <?php else: ?>
                                            <div class="module-image-placeholder" style="width: 100%; height: 160px; background-color: #eee; display: flex; align-items: center; justify-content: center; text-align: center; border-bottom: 1px solid #ccc; box-sizing: border-box;">
                                                <span style="color: #888; font-size: 1.2em; padding: 10px;"><?= h($purchase->course->title) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="module-card-content"> <?php // Added content wrapper ?>
                                        <h3 class="module-title"><?= h($purchase->course->title) ?></h3> <?php // Added class ?>
                                        <p class="module-description"><?= $this->Text->truncate(h($purchase->course->description), 100, ['ellipsis' => '...', 'exact' => false]) ?></p> <?php // Added class ?>

                                        <?php if (!empty($purchase->course->modules)): ?>
                                            <div class="module-lesson-count" style="margin-bottom: var(--space-2); color: var(--muted-text); font-size: 1.4rem;"> <?php // Added class and style ?>
                                                <i class="fas fa-layer-group fa-fw"></i> <?= count($purchase->course->modules) ?> Modules
                                            </div>
                                        <?php endif; ?>

                                        <?php // TODO: Replace with actual course progress calculation ?>
                                        <?php $courseProgress = 25; // Placeholder ?>
                                        <div class="progress-container" style="height: 1rem; margin-bottom: var(--space-4);"> <?php // Use standard progress structure ?>
                                            <div class="progress-bar" style="width: <?= $courseProgress ?>%;">
                                                <span class="progress-text" style="font-size: 0.8rem;"><?= $courseProgress ?>% Complete</span>
                                            </div>
                                        </div>
                                        <?php // Removed course-actions div, button goes in footer ?>
                                    </div>
                                    <div class="module-card-footer"> <?php // Added footer wrapper ?>
                                        <?php // Removed price display ?>
                                        <?= $this->Html->link('Continue Learning', ['controller' => 'Courses', 'action' => 'view', $purchase->course->id], ['class' => 'button button-purple button-enroll']) // Use consistent button classes ?>
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
                    <h2>You haven't purchased any courses yet.</h2>
                    <p>Explore our courses and start your financial education journey today!</p>
                    <?= $this->Html->link('Browse Courses', ['controller' => 'Courses', 'action' => 'index'], ['class' => 'button primary-button']) // Use consistent button class ?>
                </div>
            </div>
        <?php endif; ?>

        <?php /* Removed Recommended Courses Section */ ?>
    </div>

    <div class="dashboard-sidebar-content"> <?php // Sidebar column - use semantic class ?>
        <div class="card quick-links"> <?php // Added class ?>
            <div class="card-header">
                <h3>Quick Links</h3>
            </div>
            <div class="card-content"> <?php // Changed from card-body ?>
                <ul class="nav-list">
                    <li><?= $this->Html->link('Dashboard', ['controller' => 'Dashboard', 'action' => 'index']) ?></li>
                    <li class="active"><?= $this->Html->link('My Courses', ['controller' => 'Dashboard', 'action' => 'myCourses']) ?></li>
                    <li><?= $this->Html->link('My Modules', ['controller' => 'Dashboard', 'action' => 'myModules']) ?></li>
                    <li><?= $this->Html->link('Purchase History', ['controller' => 'Dashboard', 'action' => 'purchaseHistory']) ?></li>
                    <li><?= $this->Html->link('My Profile', ['controller' => 'Users', 'action' => 'profile']) ?></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Learning Tips</h3>
            </div>
            <div class="card-content"> <?php // Changed from card-body ?>
                <div class="tip-item" style="margin-bottom: var(--spacing-md);"> <?php // Added style ?>
                    <h4>Set a Schedule</h4>
                    <p>Dedicate specific times each week to your learning to maintain consistency.</p>
                </div>
                <div class="tip-item" style="margin-bottom: var(--spacing-md);"> <?php // Added style ?>
                    <h4>Take Notes</h4>
                    <p>Keep a notebook or digital document to jot down key concepts and insights.</p>
                </div>
                <div class="tip-item" style="margin-bottom: var(--spacing-md);"> <?php // Added style ?>
                    <h4>Apply What You Learn</h4>
                    <p>Try to apply the financial concepts to your own situation for better retention.</p>
                </div>
                <div class="tip-item"> <?php // Removed margin from last item ?>
                    <h4>Join Discussions</h4>
                    <p>Engage with other learners in our community forums to deepen your understanding.</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Need Help?</h3>
            </div>
            <div class="card-content"> <?php // Changed from card-body ?>
                <p>If you have any questions about your courses or need assistance, our support team is here to help.</p>
                <?= $this->Html->link('Contact Support', ['controller' => 'Pages', 'action' => 'contact'], ['class' => 'button outline-button']) // Use consistent button class ?>
            </div>
        </div>
    </div>
</div>