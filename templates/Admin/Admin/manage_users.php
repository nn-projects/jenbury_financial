<?php
/**
 * Jenbury Financial - Admin Manage Users Page
 */
$this->assign('title', 'Admin - Manage Users');
$this->Html->css(['admin/_manage-users', 'admin/_discount-codes'], ['block' => true]); // Added _discount-codes.css
?>

<div class="admin-index-page manage-users-page"> <?php // General wrapper class ?>

    <header class="admin-page-header page-header-flex"> <?php // page-header-flex for existing back button alignment ?>
        <div class="page-header-text">
            <h1><?= __('Admin - Manage Users') ?></h1>
            <p><?= __('View and manage user accounts.') ?></p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link(
                '<i class="fas fa-arrow-left"></i> ' . __('Back to Dashboard'),
                ['controller' => 'Admin', 'action' => 'dashboard'], // Corrected controller
                ['class' => 'button button-purple', 'escape' => false]
            ) ?>
        </div>
    </header>

    <div class="admin-content-card">
        <div class="admin-card-body">
            <div class="manage-users-controls" style="margin-bottom: var(--spacing-sm);">
                <div class="user-search-container">
                    <input type="search" id="user-search" class="form-control" placeholder="Search by Name or Email..." style="/* Will pick up .admin-form-container input styles */">
                    <span id="search-results-count" class="visually-hidden" aria-live="polite"></span>
                </div>
            </div>

            <div class="table-responsive user-list-container">
                <table class="user-table responsive-table"> <?php // Keep user-table for existing JS if any, new CSS will target table within .table-responsive ?>
                    <thead>
                        <tr>
                            <th scope="col" data-sort-by="name" aria-sort="none">
                                <button class="sort-button">Name <span class="sort-icon fas fa-sort"></span></button>
                            </th>
                            <th scope="col" data-sort-by="email" aria-sort="none">
                                <button class="sort-button">Email <span class="sort-icon fas fa-sort"></span></button>
                            </th>
                            <th scope="col" data-sort-by="role" aria-sort="none">
                                <button class="sort-button">Role <span class="sort-icon fas fa-sort"></span></button>
                            </th>
                            <th scope="col" data-sort-by="created" aria-sort="none">
                                <button class="sort-button">Created <span class="sort-icon fas fa-sort"></span></button>
                            </th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody id="user-list-body">
                        <?php if ($usersList->isEmpty()): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 20px;"><?= __('No users found.') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usersList as $user): ?>
                            <tr class="user-row">
                                <td data-label="Name" class="user-name"><?= h($user->first_name) ?> <?= h($user->last_name) ?></td>
                                <td data-label="Email" class="user-email"><?= h($user->email) ?></td>
                                <td data-label="Role" class="user-role-cell">
                                    <?php if ($user->role === 'admin'): ?>
                                        <span class="badge badge-primary role-badge">Administrator</span>
                                    <?php elseif($user->role === 'member'): ?>
                                        <span class="badge role-badge role-member">Member</span>
                                    <?php elseif($user->role === 'student'): ?>
                                        <span class="badge badge-success role-badge">Student</span>
                                    <?php else: ?>
                                        <?= h(ucfirst($user->role)) ?>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Created" class="user-created"><?= $user->created->format('M d, Y') ?></td>
                                <td data-label="Actions" class="actions">
                                    <?= $this->Html->link(
                                        __('Edit'), // Simpler text, icons can be added via CSS if needed
                                        ['action' => 'editUser', $user->id],
                                        ['class' => 'button button-edit'] // Matching discount codes
                                    ) ?>
                                    <?php if ($user->email !== 'admin@jenburyfinancial.com'): // Assuming this is the super admin ?>
                                        <span class="postlink-button-wrapper wrapper-danger">
                                            <?= __('Delete') ?>
                                            <?= $this->Form->postLink(
                                                '', // No visible text for the input itself
                                                ['action' => 'deleteUser', $user->id],
                                                [
                                                    'confirm' => __('Are you sure you want to delete user {0} ({1})?', h($user->first_name . ' ' . $user->last_name), h($user->email)),
                                                    'class' => 'post-link button-delete',
                                                    'escapeTitle' => false,
                                                    'title' => __('Delete User')
                                                ]
                                            ) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($user->role === 'student'): ?>
                                        <?= $this->Html->link(
                                            __('View Stats'), // Simpler text
                                            ['action' => 'viewStats', $user->id],
                                            ['class' => 'button button-info'] // Example: using a blue/info color
                                        )   ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php // Info sections removed ?>