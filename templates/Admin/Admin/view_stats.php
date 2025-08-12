<?php
/**
 * Jenbury Financial - Admin View Stats Page
 */
$this->assign('title', 'View Stats');
$this->Html->css('admin/_view-stats', ['block' => true]);
?>

<div class="view-stats-container">
    <div class="page-header page-header-flex"> <?php // Added class for flex styling ?>
        <div class="page-header-text"> <?php // Wrapper for title/description ?>
            <h1>View Student Statistics</h1>
            <p>Comprehensive Learning Metrics and Purchase History.</p>

        </div>
        
        <div class="page-header-actions"> <?php // Wrapper for the button ?>
            <?= $this->Html->link(
                '<i class="fas fa-arrow-left"></i> ' . __('Back'), // Added icon
                ['action' => 'manageUsers'],
                ['class' => 'button button-purple', 'escape' => false] // Changed to purple style
            ) ?>
        </div>
    </div>

        <div class="recent-section section-separator">
            <div class="section-header">
                <h2>Student Details</h2> 
            </div>
            <h5>Name of Student:</h5>
            <p><?= h($user->first_name) ?> <?= h($user->last_name) ?></p>
            <h5>Email:</h5>
            <p><?= h($user->email) ?></p>
            <h5>Total Number of Active Courses:</h5>
            <p><?= $totalActiveCourses ?></p>       
        </div>


        <!-- Purchase History -->   
        <div class="recent-section section-separator">
            <div class="section-header">
                <h2>Purchase History</h2>
            </div>
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Course/Module</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPurchases as $purchase): ?>
                        <tr>
                            <td><?= $purchase->created->format('M j, Y H:i') ?></td>
                            <td>
                                <?php if ($purchase->course): ?>
                                    <?= h($purchase->course->title) ?>
                                <?php else: ?>
                                    <?= h($purchase->module->title) ?>
                                <?php endif; ?>
                            </td>
                            <td class="amount">$<?= number_format($purchase->amount, 2) ?></td>
                            <td>
                                <span class="status-badge <?= strtolower(h($purchase->payment_status)) ?>">
                                    <?= h($purchase->payment_status) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Course Progress -->
        <div class="recent-section section-separator" >
            <div class="section-header">
                <h2>Course Progress</h2>
            </div>
            <?php if (isset($coursePurchases) && !$coursePurchases->isEmpty()): ?>
                <div class="course-grid">
                    <?php foreach ($coursePurchases as $purchase): ?>
                        <?php // Ensure related course data exists ?>
                        <?php if (isset($purchase->course)): ?>
                        <div class="course-card">
                            <?php if (!empty($purchase->course->image)): ?>
                                <div class="course-image">
                                    <?php // Use appropriate URL generation for images ?>
                                    <img src="<?= $this->Url->build('/img/' . h($purchase->course->image)) ?>" alt="<?= h($purchase->course->title) ?>">
                                </div>
                            <?php else: ?>
                                <?php // Render placeholder div if no image ?>
                                <div class="course-image module-image-placeholder" style="width: 100%; height: 144px; background-color: #eee; display: flex; align-items: center; justify-content: center; text-align: center; border-bottom: 1px solid #ccc; box-sizing: border-box;">
                                    <span style="color: #888; font-size: 1.1em; padding: 10px;"><?= h($purchase->course->title) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="course-details">
                                <h3><?= h($purchase->course->title) ?></h3>
                                <div class="progress-container">
                                    <div class="progress-info">
                                        <span>Progress</span>
                                        <?php // Use the calculated percentage attached to the course entity ?>
                                        <span><?= isset($purchase->course->user_progress_percentage) ? h($purchase->course->user_progress_percentage) : 0 ?>% Complete</span>
                                    </div>
                                    <?php // Apply width directly to progress-bar, remove progress-fill ?>
                                    <div class="progress-bar" style="width: <?= isset($purchase->course->user_progress_percentage) ? h($purchase->course->user_progress_percentage) : 0 ?>%;">
                                    </div>
                                    <?php $progress = $purchase->course->user_course_progress[0] ?? null; ?>
                                    <?php if (isset($purchase->course->user_progress_percentage) && $purchase->course->user_progress_percentage == 100 && !empty($progress->completion_date)):?>
                                        <strong>Completion Date: </strong>
                                        <?= h($progress->completion_date->format('M j, Y H:i')) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; // end check for $purchase->course ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #888; font-style: italic; padding: 0.75rem;">No Course Purchases!</p>
            <?php endif; // end check for $coursePurchases ?>
        </div>

        <!-- Module Progress -->
        <div class="recent-section section-separator">
            <div class="section-header">
                <h2>Module Progress</h2>
            </div>
            <?php if (isset($standaloneModulePurchases) && !$standaloneModulePurchases->isEmpty()): ?>
                <div class="course-grid">
                    <?php foreach ($standaloneModulePurchases as $purchase): ?>
                        <?php // Ensure related module data exists ?>
                        <?php if (isset($purchase->module)): ?>
                            <?php
                                $module = $purchase->module;
                                $title = h($module->title);
                                $image = $module->image ?? '';
                                $progress = $module->user_progress_percentage ?? 0;
                            ?>
                            <div class="course-card">
                                <?php if (!empty($purchase->course->image)): ?>
                                    <div class="course-image">
                                        <?php // Use appropriate URL generation for images ?>
                                        <img src="<?= $this->Url->build('/img/' . h($purchase->module->image)) ?>" alt="<?= h($purchase->module->title) ?>">
                                    </div>
                                <?php else: ?>
                                    <?php // Render placeholder div if no image ?>
                                    <div class="course-image module-image-placeholder" style="width: 100%; height: 144px; background-color: #eee; display: flex; align-items: center; justify-content: center; text-align: center; border-bottom: 1px solid #ccc; box-sizing: border-box;">
                                        <span style="color: #888; font-size: 1.1em; padding: 10px;"><?= h($purchase->module->title) ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="course-details">
                                    <h3><?= $title ?></h3>
                                    <div class="progress-container">
                                        <div class="progress-info">
                                            <span>Progress</span>
                                            <?php // Use the calculated percentage attached to the course entity ?>
                                            <span><?= isset($purchase->module->user_progress_percentage) ? h($purchase->module->user_progress_percentage) : 0 ?>% Complete</span>
                                        </div>
                                        <?php // Apply width directly to progress-bar, remove progress-fill ?>
                                        <div class="progress-bar" style="width: <?= isset($purchase->module->user_progress_percentage) ? h($purchase->module->user_progress_percentage) : 0 ?>%;">
                                        </div>
                                        <?php $progress = $purchase->module->user_module_progress[0] ?? null; ?>
                                        <?php if (isset($purchase->module->user_progress_percentage) && $purchase->module->user_progress_percentage == 100 && !empty($progress->modified)):?>
                                            <strong>Completion Date: </strong>
                                            <?= h($progress->modified->format('M j, Y H:i')) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; // end check for $purchase->course ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #888; font-style: italic; padding: 0.75rem;">No Module Purchases!</p>
            <?php endif; // end check for $coursePurchases ?>
        </div>

        <!-- Recent Activity -->

        <div class="recent-section section-separator">
            <div class="section-header">
                <h2>Recent Activity</h2>
            </div>
            <div class="card-content">
                <div class="activity-list">
                    <p id="activity-loading-message">Loading recent activity...</p>
                        <?php // Activity items will be loaded here by JavaScript ?>
                </div>
            </div>
        </div>

        <div class="recent-section">
            <div class="section-header">
                <h2>Content Progress</h2> 
            </div>

            <?php if (!empty($lastContent)): ?>
                <h5>Last Accessed Content</h5>
                <p>Content Name: <?= h($lastContent->content->title) ?></p>
                <p>Date Accessed: <?= h($lastContent->created->format('M j, Y H:i')) ?></p>
            <?php else: ?>
                <p style="color: #888; font-style: italic; padding: 0.75rem;">No Content Accessed!</p>
            <?php endif; ?>

            <?php if (!empty($recentlyFinishedContents)): ?>
                <h5>Recently Finished Contents</h5>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Content Name</th>
                                <th>Date Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentlyFinishedContents as $content): ?>
                            <tr>
                                <td>
                                    <?= h($content->content->title) ?>
                                </td>
                                <td><?= $content->created->format('M j, Y H:i') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #888; font-style: italic; padding: 0.75rem;">No Content Finished!</p>
            <?php endif; ?>
        </div>
</div>

<?php // Inject URL for JavaScript and add script tag ?>
<script>
    const activityFeedUrl = <?= json_encode($this->Url->build(['controller' => 'Admin', 'action' => 'activityFeed', $user->id])) ?>;

    document.addEventListener('DOMContentLoaded', function() {
        const activityListContainer = document.querySelector(' .activity-list');
        const loadingMessage = document.getElementById('activity-loading-message');

        if (!activityListContainer || !loadingMessage) {
            console.error('Activity feed container or loading message not found.');
            return;
        }

        fetch(activityFeedUrl)
            .then(response => {
                if (!response.ok) {
                    // Use template literal correctly now
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                loadingMessage.style.display = 'none'; // Hide loading message
                activityListContainer.innerHTML = ''; // Clear container

                if (data.activities && data.activities.length > 0) {
                    const ul = document.createElement('ul');
                    ul.style.listStyle = 'none';
                    ul.style.padding = '0';
                    ul.style.margin = '0';

                    data.activities.forEach(activity => {
                        const li = document.createElement('li');
                        li.classList.add('activity-item');
                        li.style.marginBottom = 'var(--space-4)'; // Increased margin
                        li.style.paddingBottom = 'var(--space-4)'; // Increased padding
                        li.style.borderBottom = '1px solid var(--color-border-light)';

                        let iconClass = 'icon-generic';
                        if (activity.type === 'content_completion') iconClass = 'icon-book';
                        if (activity.type === 'module_completion') iconClass = 'icon-module';
                        if (activity.type === 'course_completion') iconClass = 'icon-trophy';

                        // Use template literals correctly
                        li.innerHTML = `
                            <div style="display: flex; align-items: flex-start; gap: var(--space-3);">
                                <div class="activity-icon" style="padding-top: 2px; margin-right: var(--space-2);"> <?php // Added margin-right ?>
                                    <span class="${iconClass}" style="font-size: 1.2em; color: var(--jf-purple-primary);"></span>
                                </div>
                                <div class="activity-details" style="flex-grow: 1;">
                                    <p style="margin: 0 0 2px 0; font-size: 0.95em; color: var(--jf-dark-grey-text);">${activity.description || 'Activity recorded'}</p>
                                    <small style="color: var(--jf-grey-text); font-size: 0.85em;">${activity.time_ago || activity.formatted_timestamp || ''}</small>
                                </div>
                            </div>
                        `;
                        ul.appendChild(li);
                    });

                    if (ul.lastElementChild) {
                        ul.lastElementChild.style.borderBottom = 'none';
                        ul.lastElementChild.style.marginBottom = '0';
                        ul.lastElementChild.style.paddingBottom = '0';
                    }
                    activityListContainer.appendChild(ul);
                } else {
                    activityListContainer.innerHTML = '<p>No recent activity found.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching activity feed:', error);
                loadingMessage.style.display = 'none';
                activityListContainer.innerHTML = '<p>Could not load activity feed. Please try again later.</p>';
            });
    });
</script>


