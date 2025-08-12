<?php
/**
 * Jenbury Financial - Admin Manage Users Page
 */
$this->assign('title', 'Manage Forum Threads');
$this->Html->css('admin/_discount-codes', ['block' => true]);
$this->Html->script('admin_manage_forum_threads', ['block' => true]);
?>

<div class="admin-page-header page-header-flex"> <?php // Changed to admin-page-header, Added class for flex styling ?>
    <div class="page-header-text"> <?php // Wrapper for title/description ?>
        <h1>Manage Forum Threads</h1>
        <p>View and manage forum threads.</p>
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
                <input type="search" id="user-search" class="form-control" placeholder="Search by Title of Thread...">
                <?php // Add aria-live region for search results count if desired ?>
                <span id="search-results-count" class="visually-hidden" aria-live="polite"></span>
            </div>
            <?php // Add sorting controls for mobile if needed ?>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" data-sort-by="title" aria-sort="none">
                            <button class="sort-button">Title</button>
                        </th>
                        <th scope="col" data-sort-by="forum_category_id" aria-sort="none">
                            <button class="sort-button">Category</button>
                        </th>
                        <th scope="col" data-sort-by="user_id" aria-sort="none">
                            <button class="sort-button">User</button>
                        </th>
                        <th scope="col" data-sort-by="post_count" aria-sort="none">
                            <button class="sort-button">Number of Posts <span class="sort-icon fas fa-sort"></span></button>
                        </th>
                        <!--<th scope="col" data-sort-by="last_post_id" aria-sort="none">
                            <button class="sort-button">Last Post Id </button>
                        </th>
                       <th scope="col" data-sort-by="last_post_user_id" aria-sort="none">
                            <button class="sort-button">Last Post User Id</button>
                        </th>-->
                        <th scope="col" data-sort-by="last_post_created" aria-sort="none">
                            <button class="sort-button">Last Post Created <span class="sort-icon fas fa-sort"></span></button>
                        </th>
                        <th scope="col" data-sort-by="is_locked" aria-sort="none">
                            <button class="sort-button">Is Locked? <span class="sort-icon fas fa-sort"></span></button>
                        </th>
                        <th scope="col" data-sort-by="is_sticky" aria-sort="none">
                            <button class="sort-button">Is Sticky? <span class="sort-icon fas fa-sort"></span></button>
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
                    <?php foreach ($forumThreadsList as $forumThread): ?>
                    <tr class="user-row">
                        <td data-label="Title" class="user-title"><?= h($forumThread->title) ?></td>
                        <td data-label="Category" class="user-category"><?= h($forumThread->forum_category->title) ?></td>
                        <td data-label="User" class="user-name"><?= h($forumThread->user->first_name) ?> <?= h($forumThread->user->last_name) ?></td>
                        <td data-label="Post Count" class="user-post_count"><?= h($forumThread->post_count) ?></td>
                        <!-- <td data-label="Post Count" class="user-last_post_id"><?= h($forumThread->last_post_id) ?></td>
                        <td data-label="Post Count" class="user-last_post_user_id"><?= h($forumThread->last_post_user_id) ?></td> -->
                        <td data-label="Post Count" class="user-last_post_created"><?= h($forumThread->last_post_created) ?></td>
                        <td data-label="Is Locked" class="user-is_locked">
                                <?php if ($forumThread->is_locked): ?>
                                    <span class="status-active">Yes</span>
                                <?php else: ?>
                                    <span class="status-inactive">No</span>
                                <?php endif; ?>
                        </td>
                        <td data-label="Is Sticky" class="user-is_sticky">
                                <?php if ($forumThread->is_sticky): ?>
                                    <span class="status-active">Yes</span>
                                <?php else: ?>
                                    <span class="status-inactive">No</span>
                                <?php endif; ?>
                        </td>
                        <td data-label="Is Approved" class="user-is_approved">
                                <?php if ($forumThread->is_approved): ?>
                                    <span class="status-active">Yes</span>
                                <?php else: ?>
                                    <span class="status-inactive">No</span>
                                <?php endif; ?>
                        </td>
                        <td data-label="Created" class="user-created"><?= $forumThread->created->format('M d, Y') ?></td>
                        <td data-label="Modified" class="user-modified"><?= $forumThread->modified->format('M d, Y') ?></td>
                        <td data-label="Actions" class="actions">
                            <?= $this->Html->link(
                                '<i class="fas fa-pencil-alt"></i><span class="button-text"> ' . __('Edit') . '</span>',
                                ['action' => 'editForumThread', $forumThread->id],
                                [
                                    'class' => 'button button-edit',
                                    'escape' => false
                                ]
                            ) ?>
                            <span class="postlink-button-wrapper wrapper-danger">
                                <?= __('Delete') ?>
                                <?= $this->Form->postLink(
                                    '',
                                    ['action' => 'deleteForumThread', $forumThread->id],
                                    [
                                        'confirm' => __('Are you sure you want to delete the forum thread: {0}?', h($forumThread->title)),
                                        'class' => 'post-link button-delete',
                                        'escapeTitle' => false,
                                        'title' => __('Delete')
                                    ]
                                ) ?>
                            </span>
                            <?php if ($forumThread->is_locked): ?>
                                <span class="postlink-button-wrapper wrapper-admin-action">
                                    <?= __('Unlock') ?>
                                    <?= $this->Form->postLink(
                                        '',
                                        ['action' => 'unlockForumThread', $forumThread->id],
                                        ['class' => 'post-link button-toggle-status', 'escapeTitle' => false, 'title' => __('Unlock')]
                                    ) ?>
                                </span>
                            <?php else: ?>
                                <span class="postlink-button-wrapper wrapper-admin-action">
                                    <?= __('Lock') ?>
                                    <?= $this->Form->postLink(
                                        '',
                                        ['action' => 'lockForumThread', $forumThread->id],
                                        ['class' => 'post-link button-toggle-status', 'escapeTitle' => false, 'title' => __('Lock')]
                                    ) ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($forumThread->is_sticky): ?>
                                <span class="postlink-button-wrapper wrapper-info">
                                    <?= __('Unsticky') ?>
                                    <?= $this->Form->postLink(
                                        '',
                                        ['action' => 'unstickyForumThread', $forumThread->id],
                                        ['class' => 'post-link button-toggle-status', 'escapeTitle' => false, 'title' => __('Unsticky')]
                                    ) ?>
                                </span>
                            <?php else: ?>
                                <span class="postlink-button-wrapper wrapper-info">
                                    <?= __('Sticky') ?>
                                    <?= $this->Form->postLink(
                                        '',
                                        ['action' => 'stickyForumThread', $forumThread->id],
                                        ['class' => 'post-link button-toggle-status', 'escapeTitle' => false, 'title' => __('Sticky')]
                                    ) ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($forumThread->is_approved): ?>
                                <span class="postlink-button-wrapper wrapper-danger">
                                    <?= __('Disapprove') ?>
                                    <?= $this->Form->postLink(
                                        '',
                                        ['action' => 'disapproveForumThread', $forumThread->id],
                                        ['class' => 'post-link button-toggle-status', 'escapeTitle' => false, 'title' => __('Disapprove')]
                                    ) ?>
                                </span>
                            <?php else: ?>
                                <span class="postlink-button-wrapper wrapper-success">
                                    <?= __('Approve') ?>
                                    <?= $this->Form->postLink(
                                        '',
                                        ['action' => 'approveForumThread', $forumThread->id],
                                        ['class' => 'post-link button-toggle-status', 'escapeTitle' => false, 'title' => __('Approve')]
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