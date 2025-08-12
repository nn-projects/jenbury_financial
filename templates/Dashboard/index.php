<?php
/**
 * Jenbury Financial - Dashboard Page
 * Template file updated to match the refactored design specification.
 */
$this->assign('title', 'Dashboard');
// Link the specific dashboard CSS (assuming it's placed in webroot/css/dashboard.css)
$this->Html->css('dashboard', ['block' => true]);

?>

<div class="container">
    <div class="page-header">
        <h1>My Dashboard</h1>
    </div>

    <?php // Ensure $currentUser exists before accessing its properties ?>
    <?php if (isset($currentUser) && $currentUser): ?>
    <div class="dashboard-welcome">
        <h2>Welcome back, <?= h($currentUser->first_name) ?>!</h2>
        <p>Track your progress, access your courses, and continue your financial education journey.</p>
    </div>
    <?php endif; ?>

    <div class="dashboard-grid">
        <div class="main-content">
            <?php // Check if $coursePurchases is set and not empty ?>
            <?php // Use isEmpty() for collections/paginator results ?>
            <?php if (isset($coursePurchases) && !$coursePurchases->isEmpty()): ?>
                <div class="card">
                    <div class="card-header">
                        <h2>My Courses</h2>
                    </div>
                    <div class="card-content">
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
                                                <?php /* Optional: Add text inside if needed, like in _course-view.css */ ?>
                                                <?php /* <span class="progress-text"><?= isset($purchase->course->user_progress_percentage) ? h($purchase->course->user_progress_percentage) : 0 ?>%</span> */ ?>
                                            </div>
                                        </div>
                                        <div class="course-actions">
                                            <?= $this->Html->link('Continue Learning',
                                                ['controller' => 'Courses', 'action' => 'view', $purchase->course->id],
                                                ['class' => 'button primary-button'])
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; // end check for $purchase->course ?>
                            <?php endforeach; ?>
                        </div>
 
                    </div>
                </div>
            <?php endif; // end check for $coursePurchases ?>


            <?php // Check for standalone modules passed from controller ?>
            <?php if (isset($standaloneModulePurchases) && !$standaloneModulePurchases->isEmpty()): ?>
                <div class="card">
                    <div class="card-header">
                        <h2>My Modules</h2>
                </div>
                <div class="card-content">
                    <div class="course-grid">
                        <?php foreach ($standaloneModulePurchases as $purchase): // Iterate over filtered list ?>
                            <?php if (isset($purchase->module)): ?>
                                <?php
                                    $module = $purchase->module;
                                    $title = h($module->title);
                                    $image = $module->image ?? '';
                                    $progress = $module->user_progress_percentage ?? 0;
                                ?>
                                <div class="course-card">
                                    <?php if (!empty($image)): ?>
                                        <div class="course-image">
                                            <img src="<?= $this->Url->build('/img/' . h($purchase->module->image)) ?>" alt="<?= h($purchase->module->title) ?>">
                                        </div>
                                    <?php else: ?>
                                        <div class="course-image module-image-placeholder" style="width: 100%; height: 144px; background-color: #eee; display: flex; align-items: center; justify-content: center; text-align: center; border-bottom: 1px solid #ccc; box-sizing: border-box;">
                                            <span style="color: #888; font-size: 1.1em; padding: 10px;"><?= h($purchase->module->title) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="course-details">
                                        <h3><?= $title ?></h3>
                                        <div class="progress-container">
                                            <div class="progress-info">
                                                <span>Progress</span>
                                                <span><?= isset($purchase->module->user_progress_percentage) ? h($purchase->module->user_progress_percentage) : 0 ?>% Complete</span>
                                            </div>
                                            
                                            <div class="progress-bar" style="width: <?= isset($purchase->module->user_progress_percentage) ? h($purchase->module->user_progress_percentage) : 0 ?>%;"></div>
                                        </div>
                                        <div class="course-actions">
                                            <?= $this->Html->link('Continue Learning', ['controller' => 'Modules', 'action' => 'view', $module->id], ['class' => 'button primary-button']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>


            <?php // Show empty state only if courses are confirmed empty or not set ?>
            <?php // Show empty state only if NO courses AND NO standalone modules are purchased ?>
            <?php if ((!isset($coursePurchases) || $coursePurchases->isEmpty()) && (!isset($standaloneModulePurchases) || $standaloneModulePurchases->isEmpty())): ?>
                <div class="card">
                    <div class="card-content empty-state">
                        <h2>Start Your Learning Journey</h2>
                        <p>You haven't purchased any courses or modules yet. Explore our catalog and begin today!</p>
                        <?= $this->Html->link('Browse Courses',
                            ['controller' => 'Courses', 'action' => 'index'],
                            ['class' => 'button primary-button'])
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="sidebar">
            <div class="card quick-links">
                <div class="card-header">
                    <h3>Quick Links</h3>
                </div>
                <div class="card-content"> <?php // Keep padding for list items ?>
                    <ul class="nav-list">
                        <li><?= $this->Html->link('My Profile', ['controller' => 'Users', 'action' => 'profile']) ?></li>
                        <li><?= $this->Html->link('Purchase History', ['controller' => 'Dashboard', 'action' => 'purchaseHistory']) ?></li>
                        <li><?= $this->Html->link('Browse Courses', ['controller' => 'Courses', 'action' => 'index']) ?></li>
                    </ul>
                </div>
            </div>

            <?php // Assuming recent activity is static or passed differently ?>
            <div class="card recent-activity">
                <div class="card-header">
                    <h3>Recent Activity</h3>
                </div>
                <div class="card-content">
                    <div class="activity-list">
                        <p id="activity-loading-message">Loading recent activity...</p>
                        <?php // Activity items will be loaded here by JavaScript ?>
                    </div>
                </div>
            </div>

            <?php /* Removed Recommended Courses Section */ ?>
        </div>
    </div>
</div>

<?php // Inject URL for JavaScript and add script tag ?>
<script>
    const activityFeedUrl = <?= json_encode($this->Url->build(['controller' => 'Dashboard', 'action' => 'activityFeed'])) ?>;

    document.addEventListener('DOMContentLoaded', function() {
        const activityListContainer = document.querySelector('.recent-activity .activity-list');
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