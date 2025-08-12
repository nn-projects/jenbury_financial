<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\DiscountCode> $discountCodes
 */
$this->Html->css(['admin/_discount-codes'], ['block' => true]);
$this->assign('title', 'Admin - Discount Codes');
?>
<div class="discountCodes index content"> <?php // This outer class might still be useful for base page styling ?>

    <header class="admin-page-header">
        <h1><?= __('Admin - Discount Codes') ?></h1>
    </header>

    <div class="admin-content-card">
        <div class="admin-card-body">
            <section class="actions-section">
                <?= $this->Html->link(__('New Discount Code'), ['action' => 'add'], ['class' => 'button button-admin-primary']) ?>
            </section>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                            <th><?= $this->Paginator->sort('code', 'Code') ?></th>
                            <th class="column-percentage"><?= $this->Paginator->sort('percentage', 'Percentage') ?></th>
                            <th><?= $this->Paginator->sort('is_active', 'Status') ?></th>
                            <th><?= $this->Paginator->sort('created', 'Created') ?></th>
                            <th><?= $this->Paginator->sort('modified', 'Modified') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($discountCodes->toArray())): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;"><?= __('No discount codes found.') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($discountCodes as $discountCode): ?>
                            <tr>
                                <td><?= $this->Number->format($discountCode->id) ?></td>
                                <td><?= h($discountCode->code) ?></td>
                                <td class="column-percentage"><?= $this->Number->format($discountCode->percentage) ?>%</td>
                                <td class="<?= $discountCode->is_active ? 'status-active' : 'status-inactive' ?>">
                                    <?= $discountCode->is_active ? __('Active') : __('Inactive') ?>
                                </td>
                                <td><?= h($discountCode->created->format('M d, Y')) ?></td>
                                <td><?= h($discountCode->modified->format('M d, Y')) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $discountCode->id], ['class' => 'button button-edit']) ?>

                                    <span class="postlink-button-wrapper <?= $discountCode->is_active ? 'wrapper-warning' : 'wrapper-success' ?>">
                                        <?= $discountCode->is_active ? __('Deactivate') : __('Activate') ?>
                                        <?= $this->Form->postLink(
                                            '', // No visible text for the input itself
                                            ['action' => 'toggleStatus', $discountCode->id],
                                            [
                                                'confirm' => __('Are you sure you want to {0} code "{1}"?', ($discountCode->is_active ? 'deactivate' : 'activate'), h($discountCode->code)),
                                                'class' => 'post-link button-toggle-status ' . ($discountCode->is_active ? 'active-status' : 'inactive-status'), // Keep classes for potential JS hooks
                                                'escapeTitle' => false,
                                                'title' => $discountCode->is_active ? __('Deactivate') : __('Activate') // Tooltip for accessibility
                                            ]
                                        ) ?>
                                    </span>

                                    <span class="postlink-button-wrapper wrapper-danger">
                                        <?= __('Delete') ?>
                                        <?= $this->Form->postLink(
                                            '', // No visible text for the input itself
                                            ['action' => 'delete', $discountCode->id],
                                            [
                                                'confirm' => __('Are you sure you want to delete discount code "{0}" (ID: {1})?', h($discountCode->code), $discountCode->id),
                                                'class' => 'post-link button-delete', // Keep class for potential JS hooks
                                                'escapeTitle' => false,
                                                'title' => __('Delete') // Tooltip for accessibility
                                            ]
                                        ) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="paginator">
                <ul class="pagination">
                    <?= $this->Paginator->first('« ' . __('first')) ?>
                    <?= $this->Paginator->prev('‹ ' . __('previous')) ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next(__('next') . ' ›') ?>
                    <?= $this->Paginator->last(__('last') . ' »') ?>
                </ul>
                <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
            </div>
        </div> <!-- End admin-card-body -->
    </div> <!-- End admin-content-card -->
</div>