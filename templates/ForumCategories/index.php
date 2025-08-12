<?php
/**
 * Jenbury Financial - Admin Manage Users Page
 */
$this->assign('title', 'Manage Forum Categories');
$this->Html->css('pages/_forum-categories', ['block' => true]);
$this->Html->script('admin_manage_forum_categories', ['block' => true]);
?>

<div class="page-header page-header-flex"> <?php // Added class for flex styling ?>
    <div class="page-header-text"> <?php // Wrapper for title/description ?>
        <h1>Forum Categories</h1>
        <p>View forum categories and its related threads.</p>
    </div>
</div>


<?php if (!empty($forumCategories)): ?>
    <div class="manage-users-controls">
        <div class="user-search-container">
            <input type="search" id="user-search" class="form-control" placeholder="Search by Title of Category...">
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
                        <button class="sort-button">Title </button>
                    </th>
                    <th scope="col" data-sort-by="description" aria-sort="none">
                        <button class="sort-button">Description</button>
                    </th>
                    <th scope="col" data-sort-by="thread_count" aria-sort="none">
                        <button class="sort-button">Number of Threads <span class="sort-icon fas fa-sort"></span></button>
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
                <?php foreach ($forumCategories as $forumCategory): ?>
                <tr class="user-row">
                    <td data-label="Title" class="user-name"><?= h($forumCategory->title) ?></td>
                    <td data-label="Description" class="user-description"><?= h($forumCategory->description) ?></td>
                    <td data-label="Thread Count" class="user-thread_count"><?= count($forumCategory->forum_threads) ?></td>
                    <td data-label="Created" class="user-created"><?= $forumCategory->created->format('M d, Y') ?></td>
                    <td data-label="Modified" class="user-modified"><?= $forumCategory->modified->format('M d, Y') ?></td>
                    <td data-label="Actions" class="threads"> <?php // Removed inline style ?>
                    <?= $this->Html->link(
                            '<i class="fas fa-eye"></i><span class="button-text"> ' . __('View Threads') . '</span>', // Added text span
                            ['action' => 'view', $forumCategory->id],
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
    <h3>There are no forum categories yet!</h3>
<?php endif; ?>

<?= $this->fetch('script') ?>