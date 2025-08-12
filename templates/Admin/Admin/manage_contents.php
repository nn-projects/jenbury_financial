<?php
/**
 * Jenbury Financial - Admin Manage Contents Page
 */
$this->assign('title', 'Manage Module Contents');
$this->Html->css(['admin/_manage-contents'], ['block' => true]); // Added CSS link
?>

<div class="manage-contents-page admin-manage-page"> <?php // Added page wrapper class ?>

    <header class="admin-page-header">
        <h1>Manage Contents for: <?= h($module->title) ?></h1>
        <div class="header-page-actions">
            <?= $this->Html->link('Add New Lesson Content',
                ['action' => 'addContent', $module->id],
                ['class' => 'button button-primary']
            ) ?>
            <?= $this->Html->link('‹ Back to Modules',
                ['action' => 'manageModules', $module->course_id],
                ['class' => 'button', 'escape' => false]
            ) ?>
        </div>
    </header>

    <div class="module-info admin-content-card"> <?php // Standardized card classes ?>
        <div class="admin-card-header">
            <h3>Module Information</h3> <?php // Changed to h3 for card header consistency ?>
        </div>
        <div class="admin-card-body">
            <p><strong>Course:</strong> <?= h($module->course->title) ?></p>
            <p><strong>Module:</strong> <?= h($module->title) ?></p>
            <p><strong>Description:</strong> <?= h($module->description) ?></p> <?php // Removed truncate to show full description ?>
        </div>
    </div>

    <div class="contents admin-content-card"> <?php // Standardized card classes ?>
        <div class="admin-card-header">
            <h3>Lesson Contents</h3> <?php // Changed to h3 ?>
            <?php // Add button was moved to page header ?>
        </div>
        <div class="admin-card-body">
            <?php if (!empty($module->contents)): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($module->contents as $content): ?>
                            <tr>
                                <td><?= $content->order ?></td>
                                <td><?= h($content->title) ?></td>
                                <td><?= h($content->type) ?></td>
                                <td>
                                    <?php if ($content->is_active): ?>
                                        <span class="status-active">Active</span>
                                    <?php else: ?>
                                        <span class="status-inactive">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $content->created->format('M d, Y') ?></td>
                                <td class="actions">
                                    <?= $this->Html->link('Edit',
                                        ['action' => 'editLessonContent', $content->id],
                                        ['class' => 'button button-small']
                                    ) ?>
                                    <?= $this->Form->postLink('Delete',
                                        ['action' => 'deleteContent', $content->id],
                                        [
                                            'confirm' => 'Are you sure you want to delete this content?',
                                            'class' => 'button button-small button-outline button-delete'
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-content">No content items found. Click "Add New Content" to add content to this module.</p>
        <?php endif; ?>
    </div>
</div>

<?php // Removed the "admin-actions" div with "Content Management" and "Content Statistics" cards ?>
</div> <?php // Close manage-contents-page ?>
<?php // Removed inline style block, styles will be in _manage-contents.css ?>