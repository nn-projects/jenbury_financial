<!-- filepath: C:\xampp\htdocs\team109-app_fit3048\JenburyFinancial\templates\ContentBlocks\index.php -->
<?php
/**
 * @var \App\View\AppView $this
 * @var array $groupedBlocks Array of ContentBlock entities grouped by parent.
 */
?>

<div class="content-blocks index">
    <div class="page-header-actions"> <!-- New container for layout -->
        <h1><?= __('Content Blocks') ?></h1>
        <?= $this->Form->postLink(
            __('Restore All'),
            ['action' => 'restoreAll'],
            [
                'confirm' => __('Are you sure you want to restore ALL content blocks to their previous values? This cannot be undone.'),
                'class' => 'button button-danger button-small' // Style as a small danger button
            ]
        ) ?>
    </div>

    <?php if (!empty($groupedBlocks)): ?>
        <?php foreach ($groupedBlocks as $parent => $blocks): ?>
            <section class="content-group">
                <h2<?= in_array($parent, ['Home', 'Navigation', 'Footer']) ? ' class="content-group-heading-large"' : '' ?>><?= h($parent ?: __('Ungrouped')) ?></h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?= __('Description') ?></th>
                            <th><?= __('Value') ?></th>
                            <th><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($blocks as $block): ?>
                            <tr>
                                <td data-label="<?= __('Description') ?>"><?= h($block->description) ?></td>
                                <td data-label="<?= __('Value') ?>"><?= h(mb_strimwidth($block->value, 0, 50, '...')) ?></td>
                                <td data-label="<?= __('Actions') ?>">
                                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $block->id], ['class' => 'button']) ?>
                                    <?= $this->Form->postLink(__('Restore'), ['action' => 'restore', $block->id], [
                                        'confirm' => __('Are you sure you want to restore this block?'),
                                        'class' => 'button button-outline'
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <p><?= __('No content blocks found.') ?></p>
    <?php endif; ?>
</div>