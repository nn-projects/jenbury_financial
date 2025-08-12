<?php
$this->assign('title', 'Manage Courses');
?>

<div class="manage-courses-page"> <?php // Added wrapper class ?>
    <div class="container">
        <div class="header-actions">
            <h2>Manage Courses</h2>
        <?= $this->Html->link('Add New Course', 
            ['action' => 'addCourse'], 
            ['class' => 'button button-primary']
        ) ?>
    </div>

    <div class="courses-list">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Modules</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coursesList as $course): ?>
                <tr>
                    <td data-label="Title"><?= h($course->title) ?></td>
                    <td data-label="Price">$<?= number_format($course->price, 2) ?></td>
                    <td data-label="Modules"><?= count($course->modules) ?></td>
                    <td data-label="Status">
                        <?php if ($course->is_active): ?>
                            <span class="status-active">Active</span>
                        <?php else: ?>
                            <span class="status-inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Actions" class="actions">
                        <?= $this->Html->link('Edit', 
                            ['action' => 'editCourse', $course->id],
                            ['class' => 'button button-small']
                        ) ?>
                        
                        <?= $this->Html->link('Manage Modules', 
                            ['action' => 'manageModules', $course->id],
                            ['class' => 'button button-small']
                        ) ?>
                        
                        <?= $this->Form->postLink('Delete', 
                            ['action' => 'deleteCourse', $course->id],
                            [
                                'confirm' => 'Are you sure you want to delete this course?',
                                'class' => 'button button-small button-danger'
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="back-link">
        <?= $this->Html->link('Back to Dashboard', 
            ['action' => 'dashboard'],
            ['class' => 'button']
        ) ?>
    </div>
</div> <?php // Closed wrapper class div ?>