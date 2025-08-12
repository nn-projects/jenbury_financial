<?php
$this->assign('title', 'Admin Dashboard');
$this->Html->css('admindashboard', ['block' => true]);
?>

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <div class="header-stats">
            <?= $this->Html->link('View Site', '/', ['class' => 'button view-site', 'target' => '_blank']) ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <?php /*
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <h3>Users</h3>
                <div class="stat-number"><?= $userCount ?></div>
            </div>
        </div>
        */ ?>
        <?php /*
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <div class="stat-content">
                <h3>Courses</h3>
                <div class="stat-number"><?= $courseCount ?></div>
            </div>
        </div>
        */ ?>
        <?= $this->Html->link(
            '<div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <h3>Users</h3>
                    <div class="stat-number">' . $userCount . '</div>
                </div>
            </div>',
            ['action' => 'manageUsers'],
            ['escape' => false]
        ) ?>

        <?= $this->Html->link(
            '<div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-content">
                    <h3>Courses</h3>
                    <div class="stat-number">' . $courseCount . '</div>
                </div>
            </div>',
            ['action' => 'manageCourses'],
            ['escape' => false]
        ) ?>
        
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
            <div class="stat-content">
                <h3>Modules</h3>
                <div class="stat-number"><?= $moduleCount ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-content">
                <h3>Completed Purchases</h3>
                <div class="stat-number"><?= $purchaseCount ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-content">
                <h3>Total Revenue</h3>
                <div class="stat-number">$<?= number_format($revenueTotal, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-container">
        <h2 class="section-title">Quick Actions</h2>
        <div class="action-grid">
            <?= $this->Html->link('Manage Users', ['action' => 'manageUsers'], [
                'class' => 'action-card',
                'escape' => false
            ]) ?>
            <?= $this->Html->link('Manage Courses', ['action' => 'manageCourses'], [
                'class' => 'action-card',
                'escape' => false
            ]) ?>
            <?= $this->Html->link('Add New Course', ['action' => 'addCourse'], [
                'class' => 'action-card',
                'escape' => false
            ]) ?>
            <?= $this->Html->link('Manage Forum Categories', ['action' => 'manageForumCategories'], [
                'class' => 'action-card',
                'escape' => false
            ]) ?>
            <?= $this->Html->link('Manage Forum Threads', ['action' => 'manageForumThreads'], [
                'class' => 'action-card',
                'escape' => false
            ]) ?>
            <?= $this->Html->link('Manage Forum Posts', ['action' => 'manageForumPosts'], [
                'class' => 'action-card',
                'escape' => false
            ]) ?>
<?= $this->Html->link('Manage Discount Codes', ['controller' => 'DiscountCodes', 'action' => 'index', 'prefix' => 'Admin'], [
                'class' => 'action-card',
                'escape' => false
            ]) ?>
        </div>
    </div>

    <!-- Recent Purchases -->
    <div class="recent-section">
        <div class="section-header">
            <h2>Recent Purchases</h2>
        </div>
        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
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
                            <div class="user-info">
                                <div class="user-avatar"><?= strtoupper(substr($purchase->user->email, 0, 1)) ?></div>
                                <?= h($purchase->user->email) ?>
                            </div>
                        </td>
                        <td>
                            <?php 
                            if ($purchase->course && isset($purchase->course->title)): // Check if course and title exist
                                echo h($purchase->course->title);
                            elseif ($purchase->module && isset($purchase->module->title)): // Check if module and title exist
                                echo h($purchase->module->title);
                            else:
                                echo '[Deleted Item]';
                            endif;
                            ?>
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
</div>
