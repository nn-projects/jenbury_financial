<?php
/**
 * Jenbury Financial - Admin Manage Users Page
 */
use Cake\Utility\Text;
$this->assign('title', 'Manage Forum Posts');
$this->Html->css('admin/_discount-codes', ['block' => true]);
$this->Html->script('admin_manage_forum_posts', ['block' => true]);
?>

<div class="admin-page-header page-header-flex"> <?php // Changed to admin-page-header, Added class for flex styling ?>
    <div class="page-header-text"> <?php // Wrapper for title/description ?>
        <h1>Manage Forum Posts</h1>
        <p>View and manage forum posts.</p>
    </div>
    <div class="page-header-actions">
        <?= $this->Html->link(
            '<i class="fas fa-arrow-left"></i> ' . __('Back to Dashboard'),
            ['action' => 'dashboard'],
            ['class' => 'button button-admin-primary', 'escape' => false]
        ) ?>
    </div>
</div>

<div class="admin-content-card">
    <div class="admin-card-body">
        <div class="manage-users-controls">
            <div class="user-search-container">
                <input type="search" id="user-search" class="form-control" placeholder="Search by User Who Made Post...">
                <?php // Add aria-live region for search results count if desired ?>
                <span id="search-results-count" class="visually-hidden" aria-live="polite"></span>
            </div>
            <?php // Add sorting controls for mobile if needed ?>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" data-sort-by="forum_thread_id" aria-sort="none">
                            <button class="sort-button">Thread</button>
                        </th>
                        <th scope="col" data-sort-by="user_id" aria-sort="none">
                            <button class="sort-button">User</button>
                        </th>
                        <th scope="col" data-sort-by="content" aria-sort="none">
                            <button class="sort-button">Content</button>
                        </th>
                        <th scope="col" data-sort-by="is_approved" aria-sort="none">
                            <button class="sort-button">Is Approved? <span class="sort-icon fas fa-sort"></span></button>
                        </th>
                        <th scope="col" data-sort-by="created" aria-sort="none">
                            <button class="sort-button">Created <span class="sort-icon fas fa-sort"></span></button>
                        </th>
                        <th scope="col" data-sort-by="modified" aria-sort="none">
                            <button class="sort-button">Modified <span class="sort-icon fas fa-sort"></span></button>
                        </th>
                        <th scope="col">Actions</th> <?php // Actions column usually not sortable ?>
                    </tr>
                </thead>
                <tbody id="user-list-body">
                    <?php foreach ($forumPostsList as $forumPost): ?>
                    <tr class="user-row">
                        <td data-label="Thread" class="user-thread"><?= h($forumPost->forum_thread->title) ?></td>
                        <td data-label="User" class="user-name"><?= h($forumPost->user->first_name) ?> <?= h($forumPost->user->last_name) ?></td>
                        <td data-label="Content" class="user-content"><?= h(Text::truncate($forumPost->content, 100)) ?></td>
                        <td data-label="Is Approved" class="user-is_approved">
                                <?php if ($forumPost->is_approved): ?>
                                    <span class="status-active">Yes</span>
                                <?php else: ?>
                                    <span class="status-inactive">No</span>
                                <?php endif; ?>
                        </td>
                        <td data-label="Created" class="user-created"><?= $forumPost->created->format('M d, Y') ?></td>
                        <td data-label="Modified" class="user-modified"><?= $forumPost->modified->format('M d, Y') ?></td>
                        <td data-label="Actions" class="actions">
                            <?= $this->Html->link(
                                '<i class="fas fa-pencil-alt"></i><span class="button-text"> ' . __('Edit') . '</span>',
                                ['action' => 'editForumPost', $forumPost->id],
                                [
                                    'class' => 'button button-edit', // Changed to match yellow/orange style
                                    'escape' => false
                                ]
                            ) ?>
                            <span class="postlink-button-wrapper wrapper-danger">
                                <?= __('Delete') ?>
                                <?= $this->Form->postLink(
                                    '', // Text moved out
                                    ['action' => 'deleteForumPost', $forumPost->id],
                                    [
                                        'confirm' => __('Are you sure you want to delete the forum post by {0}?', h($forumPost->user->first_name . ' ' . $forumPost->user->last_name) ),
                                        'class' => 'post-link button-delete', // Standardized class
                                        'escapeTitle' => false, // Added
                                        'title' => __('Delete') // Added
                                    ]
                                ) ?>
                            </span>
                            <?php if ($forumPost->is_approved): ?>
                                <span class="postlink-button-wrapper wrapper-danger">
                                    <?= __('Disapprove') ?>
                                    <?= $this->Form->postLink(
                                        '', // Text moved out
                                        ['action' => 'disapproveForumPost', $forumPost->id],
                                        [
                                            'class' => 'post-link button-toggle-status', // Standardized class
                                            'escapeTitle' => false, // Added
                                            'title' => __('Disapprove') // Added
                                        ]
                                    ) ?>
                                </span>
                            <?php else: ?>
                                <span class="postlink-button-wrapper wrapper-success">
                                    <?= __('Approve') ?>
                                    <?= $this->Form->postLink(
                                        '', // Text moved out
                                        ['action' => 'approveForumPost', $forumPost->id],
                                        [
                                            'class' => 'post-link button-toggle-status', // Standardized class
                                            'escapeTitle' => false, // Added
                                            'title' => __('Approve') // Added
                                        ]
                                    ) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->fetch('script') ?>