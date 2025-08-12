<?php
$this->assign('title', 'Manage Modules');
$this->Html->css('admin/_manage-module.css', ['block' => true]);
?>
<style>
    .modules-list-sortable {
        list-style: none;
        padding: 0;
    }
    .module-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background-color: #fff;
        border: 1px solid #ddd;
        margin-bottom: -1px; /* Creates a collapsed border effect */
    }
    .module-item:first-child {
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }
    .module-item:last-child {
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
        margin-bottom: 0;
    }
    .grab-handle {
        cursor: grab;
        font-size: 1.5rem;
        margin-right: 1rem;
        color: #aaa;
    }
    .module-info {
        flex-grow: 1;
    }
    .module-actions {
        margin-left: auto;
    }
    .sortable-ghost {
        opacity: 0.4;
        background-color: #cce5ff;
    }
    .module-item .button {
        color: #fff !important; /* Ensure text is white */
    }
    .module-actions .button-edit {
        width: 100px; /* Increase width of the edit button */
    }
</style>

<div class="mm-container admin-manage-page">
    <header class="admin-page-header">
        <h1>Manage Modules: <?= h($course->title) ?></h1>
        <div class="header-page-actions">
            <?= $this->Html->link(
                'Add New Module',
                ['action' => 'addModule', $course->id],
                ['class' => 'button button-primary']
            ) ?>
            <?= $this->Html->link(
                '‹ ' . __('Back to Courses'),
                ['action' => 'manageCourses'],
                ['class' => 'button', 'escape' => false]
            ) ?>
        </div>
    </header>

    <?php if (!empty($course->modules)): ?>
        <div class="admin-content-card">
            <div class="admin-card-body">
                <ul id="module-list" class="modules-list-sortable" data-course-id="<?= $course->id ?>">
                    <?php foreach ($course->modules as $module): ?>
                        <li class="module-item" data-module-id="<?= $module->id ?>">
                            <span class="grab-handle">⋮⋮</span>
                            <div class="module-info">
                                <strong><?= h($module->title) ?></strong>
                                <small>(<?= isset($module->contents) ? count($module->contents) : 0 ?> lessons)</small>
                                <br>
                                <span class="status-<?= $module->is_active ? 'active' : 'inactive' ?>">
                                    <?= $module->is_active ? 'Active' : 'Inactive' ?>
                                </span>
                            </div>
                            <div class="module-actions actions">
                                <?= $this->Html->link('Edit', ['action' => 'editModule', $module->id], ['class' => 'button button-small button-edit']) ?>
                                <?= $this->Html->link('Manage Lessons', ['action' => 'manageContents', $module->id], ['class' => 'button button-small']) ?>
                                <?= $this->Form->postLink('Delete', ['action' => 'deleteModule', $module->id], ['confirm' => 'Are you sure you want to delete this module?', 'class' => 'button button-small button-danger']) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state admin-content-card"> <?php // Added admin-content-card for styling ?>
            <div class="admin-card-body"> <?php // Added admin-card-body for padding ?>
                <p>No modules have been created for this course yet.</p>
                <p><?= $this->Html->link(
                    'Add your first module',
                    ['action' => 'addModule', $course->id],
                    ['class' => 'button button-primary']
                ) ?></p>
            </div>
        </div>
    <?php endif; ?>
    <?php // Back link was moved to the header ?>
</div>