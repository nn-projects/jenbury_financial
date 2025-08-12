<?php
/**
 * Jenbury Financial - View Forum Threads Page
 */
$this->assign('title', 'View Forum Threads');
$this->Html->css('pages/_forum-threads', ['block' => true]);
$this->Html->script('admin_manage_forum_threads', ['block' => true]);
?>

<div class="page-header page-header-flex"> <?php // Added class for flex styling ?>
    <div class="page-header-text"> <?php // Wrapper for title/description ?>
        <h1>Forum Threads for Forum Category: <?= h($forumCategory->title) ?></h1>
        <p>View related threads.</p>
    </div>
    <div class="page-header-actions"> <?php // Wrapper for the button ?>
        <?= $this->Html->link('Add New Forum Thread', 
            ['controller' => 'ForumThreads', 'action' => 'add', $forumCategory->id], 
            ['class' => 'button button-primary']
        ) ?>
        
    </div>
</div>


<?php if (!empty($forumCategory->forum_threads)): ?>
    <div class="manage-users-controls">
        <div class="user-search-container">
            <input type="search" id="user-search" class="form-control" placeholder="Search by Title of Thread...">
            <?php // Add aria-live region for search results count if desired ?>
            <span id="search-results-count" class="visually-hidden" aria-live="polite"></span>
        </div>
        <?php // Add sorting controls for mobile if needed ?>
    </div>
    <div class="user-list-container"> <?php // Renamed from card/table-responsive for clarity ?>
        <table class="user-table responsive-table">
            <thead>
                <tr>
                    <th scope="col" data-sort-by="title" aria-sort="none">
                        <button class="sort-button">Title</button>
                    </th>
                    <th scope="col" data-sort-by="forum_category_id" aria-sort="none">
                        <button class="sort-button">Category</button>
                    </th>
                    <th scope="col" data-sort-by="user_id" aria-sort="none">
                        <button class="sort-button">Created By</button>
                    </th>
                    <th scope="col" data-sort-by="post_count" aria-sort="none">
                        <button class="sort-button">Number of Posts <span class="sort-icon fas fa-sort"></span></button>
                    </th>
                    <th scope="col" data-sort-by="is_sticky" aria-sort="none">
                        <button class="sort-button">Is Sticky? <span class="sort-icon fas fa-sort"></span></button>
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
                <?php foreach ($forumCategory->forum_threads as $forumThread): ?>
                <tr class="user-row">
                    <td data-label="Title" class="user-title"><?= h($forumThread->title) ?></td>
                    <td data-label="Category" class="user-forum_category_id"><?= h($forumCategory->title) ?></td>
                    <td data-label="User" class="user-user_id"><?= h($forumThread->user->first_name) ?> <?= h($forumThread->user->last_name) ?></td>
                    <td data-label="Post Count" class="user-post_count"><?= count($forumThread->forum_posts) ?></td>
                    <td data-label="Is Sticky" class="user-is_sticky">
                            <?php if ($forumThread->is_sticky): ?>
                                <span class="status-active">Yes</span>
                            <?php else: ?>
                                <span class="status-inactive">No</span>
                            <?php endif; ?>
                    </td>
                    <td data-label="Created" class="user-created"><?= $forumThread->created->format('M d, Y') ?></td>
                    <td data-label="Modified" class="user-modified"><?= $forumThread->modified->format('M d, Y') ?></td>
                    <td data-label="Actions" class="actions"> <?php // Removed inline style ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-eye"></i><span class="button-text"> ' . __('View Posts') . '</span>', // Added text span
                            ['controller' => 'ForumThreads', 'action' => 'view', $forumThread->id],
                            [
                                'class' => 'button button-small',
                                'escape' => false // Needed to render HTML icon
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <h3>There are no forum threads for this category yet!</h3>
<?php endif; ?>

<div class="back-button">
    <?= $this->Html->link(
        '<i class="fas fa-arrow-left"></i> ' . __('Back'), // Added icon
        ['action' => 'index'],
        ['class' => 'button button-purple', 'escape' => false] // Changed to purple style
    ) ?>
</div>
<?= $this->fetch('script') ?>