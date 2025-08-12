<?php
/**
 * Jenbury Financial - Admin Manage Users Page
 */
$this->assign('title', 'Manage Forum Categories');
$this->Html->css('admin/_discount-codes', ['block' => true]);
$this->Html->script('admin_manage_forum_categories', ['block' => true]);
?>

<div class="admin-page-header page-header-flex"> <?php // Changed to admin-page-header, Added class for flex styling ?>
    <div class="page-header-text"> <?php // Wrapper for title/description ?>
        <h1>Manage Forum Categories</h1>
        <p>View and manage forum categories.</p>
    </div>
</div>

<div class="admin-content-card">
    <div class="admin-card-body">
        <section class="actions-section">
            <?= $this->Html->link('Add New Forum Category',
                ['action' => 'addForumCategory'],
                ['class' => 'button button-admin-primary']
            ) ?>
        </section>

        <div class="manage-users-controls">
            <div class="user-search-container">
                <input type="search" id="user-search" class="form-control" placeholder="Search by Title of Category...">
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
                        <th scope="col" data-sort-by="thread_count" aria-sort="none">
                            <button class="sort-button">Number of Threads (Approved+Disapproved) <span class="sort-icon fas fa-sort"></span></button>
                        </th>
                        <th scope="col" data-sort-by="post_count" aria-sort="none">
                            <button class="sort-button">Number of Posts <span class="sort-icon fas fa-sort"></span></button>
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
                    <?php foreach ($forumCategoriesList as $forumCategory): ?>
                    <tr class="user-row">
                        <td data-label="Title" class="user-title"><?= h($forumCategory->title) ?></td>
                        <td data-label="Thread Count" class="user-thread_count"><?= h($forumCategory->thread_count) ?></td>
                        <td data-label="Post Count" class="user-post_count"><?= h($forumCategory->post_count) ?></td>
                        <td data-label="Created" class="user-created"><?= $forumCategory->created->format('M d, Y') ?></td>
                        <td data-label="Modified" class="user-modified"><?= $forumCategory->modified->format('M d, Y') ?></td>
                        <td data-label="Actions" class="actions">
                            <?= $this->Html->link(
                                '<i class="fas fa-pencil-alt"></i><span class="button-text"> ' . __('Edit') . '</span>',
                                ['action' => 'editForumCategory', $forumCategory->id],
                                [
                                    'class' => 'button button-edit',
                                    'escape' => false
                                ]
                            ) ?>
                            <span class="postlink-button-wrapper wrapper-danger">
                                <?= __('Delete') ?>
                                <?= $this->Form->postLink(
                                    '',
                                    ['action' => 'deleteForumCategory', $forumCategory->id],
                                    [
                                        'confirm' => __('Are you sure you want to delete the forum category: {0} ?', h($forumCategory->title)),
                                        'class' => 'post-link button-delete',
                                        'escapeTitle' => false,
                                        'title' => __('Delete')
                                    ]
                                ) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="back-button">
    <?= $this->Html->link(
        '<i class="fas fa-arrow-left"></i> ' . __('Back to Dashboard'),
        ['action' => 'dashboard'],
        ['class' => 'button button-admin-primary', 'escape' => false]
    ) ?>
</div>

<?= $this->fetch('script') ?>