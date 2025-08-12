<?php
/**
 * Jenbury Financial - View Forum Threads Page
 */
use Cake\Utility\Text;
$this->assign('title', 'View Forum Posts');
$this->Html->css('pages/_forum-posts', ['block' => true]);
$this->Html->script('admin_manage_forum_posts', ['block' => true]);

// Get the current user
$user = $this->request->getAttribute('identity')->getIdentifier();

?>

<div class="page-header page-header-flex"> <?php // Added class for flex styling ?>
    <div class="page-header-text"> <?php // Wrapper for title/description ?>
        <h1>Forum Posts for Forum Thread: <?= h($forumThread->title) ?></h1>
        <p>View related posts.</p>
    </div>
    <div class="page-header-actions"> <?php // Wrapper for the button ?>
        <?php if (!$forumThread->is_locked): ?>
            <?= $this->Html->link('Add New Post', 
                ['controller' => 'ForumPosts', 'action' => 'add', $forumThread->id], 
                ['class' => 'button button-primary']
            ) ?>
        <?php endif; ?>        
    </div>
</div>

<?php if (!empty($forumThread->forum_posts)): ?>
    <div class="manage-users-controls">
        <div class="user-search-container">
            <input type="search" id="user-search" class="form-control" placeholder="Search by User Who Made Post...">
            <?php // Add aria-live region for search results count if desired ?>
            <span id="search-results-count" class="visually-hidden" aria-live="polite"></span>
        </div>
        <?php // Add sorting controls for mobile if needed ?>
    </div>

    <div class="user-list-container"> <?php // Renamed from card/table-responsive for clarity ?>
        <table class="user-table responsive-table">
            <thead>
                <tr>
                    <th scope="col" data-sort-by="user_id" aria-sort="none">
                        <button class="sort-button">Created By</button>
                    </th>
                    <th scope="col" data-sort-by="content" aria-sort="none">
                        <button class="sort-button">Content</button>
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
                <?php foreach ($forumThread->forum_posts as $forumPost): ?>
                <tr class="user-row">
                    <td data-label="User" class="user-name"><?= h($forumPost->user->first_name) ?> <?= h($forumPost->user->last_name) ?></td>
                    <td data-label="Content" class="user-content"><?= h(Text::truncate($forumPost->content, 100)) ?></td>
                    <td data-label="Created" class="user-created"><?= $forumPost->created->format('M d, Y') ?></td>
                    <td data-label="Modified" class="user-modified"><?= $forumPost->modified->format('M d, Y') ?></td>
                    <td data-label="Actions" class="actions"> <?php // Removed inline style ?>
                        <?php if ($forumPost->user_id === $user): ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-pencil-alt"></i><span class="button-text"> ' . __('Edit') . '</span>', // Added text span
                                ['controller' =>'ForumPosts' , 'action' => 'edit', $forumPost->id],
                                [
                                    'class' => 'button button-small',
                                    'escape' => false // Needed to render HTML icon
                                ]
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="fas fa-trash-alt"></i><span class="button-text"> ' . __('Delete') . '</span>', // Added text span
                                ['controller' =>'ForumPosts' , 'action' => 'delete', $forumPost->id],
                                [
                                    'confirm' => __('Are you sure you want to delete post # {0}?', $forumPost->id),
                                    'class' => 'button button-small button-danger', // Added danger class for styling
                                    'escape' => false, // Needed to render HTML icon
                                    'block' => false // Keep inline
                                ]
                            ) ?>
                        <?php endif ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <h3>There are no forum posts for this thread yet!</h3>
<?php endif; ?>

<div class="back-button">
    <?= $this->Html->link(
        '<i class="fas fa-arrow-left"></i> ' . __('Back'), // Added icon
        ['controller' => 'ForumCategories' ,'action' => 'index'],
        ['class' => 'button button-purple', 'escape' => false] // Changed to purple style
    ) ?>
</div>
<?= $this->fetch('script') ?>