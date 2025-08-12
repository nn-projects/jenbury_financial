<?php
/**
 * Jenbury Financial - Purchase History Page
 */
$this->assign('title', 'Purchase History');
?>

<div class="page-header">
    <h1>Purchase History</h1>
</div>

<div class="row">
    <div class="column column-75">
        <div class="card">
            <div class="card-header">
                <h2>Your Purchases</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($purchases)): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Item</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchases as $purchase): ?>
                                    <tr>
                                        <td><?= $purchase->created->format('M d, Y') ?></td>
                                        <td>
                                            <?php if ($purchase->course): ?>
                                                <?= h($purchase->course->title) ?>
                                            <?php elseif ($purchase->module): ?>
                                                <?= h($purchase->module->title) ?>
                                            <?php else: ?>
                                                Unknown Item
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($purchase->course): ?>
                                                Course
                                            <?php elseif ($purchase->module): ?>
                                                Module
                                            <?php else: ?>
                                                Unknown
                                            <?php endif; ?>
                                        </td>
                                        <td>$<?= number_format($purchase->amount, 2) ?></td>
                                        <td>
                                            <?php if ($purchase->payment_status === 'completed'): ?>
                                                <span class="badge badge-success">Completed</span>
                                            <?php elseif ($purchase->payment_status === 'pending'): ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php elseif ($purchase->payment_status === 'failed'): ?>
                                                <span class="badge badge-danger">Failed</span>
                                            <?php elseif ($purchase->payment_status === 'refunded'): ?>
                                                <span class="badge badge-info">Refunded</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($purchase->payment_status === 'completed'): ?>
                                                <?php if ($purchase->course): ?>
                                                    <?= $this->Html->link('View Course', ['controller' => 'Courses', 'action' => 'view', $purchase->course->id], ['class' => 'button button-small']) ?>
                                                <?php elseif ($purchase->module): ?>
                                                    <?= $this->Html->link('View Module', ['controller' => 'Modules', 'action' => 'view', $purchase->module->id], ['class' => 'button button-small']) ?>
                                                <?php endif; ?>
                                            <?php elseif ($purchase->payment_status === 'pending'): ?>
                                                <?= $this->Html->link('Complete Payment', '#', ['class' => 'button button-small']) ?>
                                            <?php elseif ($purchase->payment_status === 'failed'): ?>
                                                <?= $this->Html->link('Try Again', '#', ['class' => 'button button-small']) ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pagination-container">
                        <ul class="pagination">
                            <?= $this->Paginator->prev('« Previous') ?>
                            <?= $this->Paginator->numbers() ?>
                            <?= $this->Paginator->next('Next »') ?>
                        </ul>
                        <p><?= $this->Paginator->counter('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total') ?></p>
                    </div>
                <?php else: ?>
                    <div class="no-purchases">
                        <p>You haven't made any purchases yet.</p>
                        <?= $this->Html->link('Browse Courses', ['controller' => 'Courses', 'action' => 'index'], ['class' => 'button']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="column column-25">
        <div class="card">
            <div class="card-header">
                <h3>Quick Links</h3>
            </div>
            <div class="card-body">
                <ul class="nav-list">
                    <li><?= $this->Html->link('Dashboard', ['controller' => 'Dashboard', 'action' => 'index']) ?></li>
                    <li><?= $this->Html->link('My Courses', ['controller' => 'Dashboard', 'action' => 'myCourses']) ?></li>
                    <li><?= $this->Html->link('My Modules', ['controller' => 'Dashboard', 'action' => 'myModules']) ?></li>
                    <li class="active"><?= $this->Html->link('Purchase History', ['controller' => 'Dashboard', 'action' => 'purchaseHistory']) ?></li>
                    <li><?= $this->Html->link('My Profile', ['controller' => 'Users', 'action' => 'profile']) ?></li>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Need Help?</h3>
            </div>
            <div class="card-body">
                <p>If you have any questions about your purchases or need assistance, our support team is here to help.</p>
                <?= $this->Html->link('Contact Support', ['controller' => 'Pages', 'action' => 'contact'], ['class' => 'button button-outline']) ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Payment Methods</h3>
            </div>
            <div class="card-body">
                <p>We accept the following payment methods:</p>
                <ul class="payment-methods">
                    <li><i class="icon-visa"></i> Visa</li>
                    <li><i class="icon-mastercard"></i> Mastercard</li>
                    <li><i class="icon-amex"></i> American Express</li>
                    <li><i class="icon-paypal"></i> PayPal</li>
                </ul>
            </div>
        </div>
    </div>
</div>