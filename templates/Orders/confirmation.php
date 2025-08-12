<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order // Passed from OrdersController::confirmation
 */

$this->layout = 'jenbury'; // Or your default layout
$this->assign('title', __('Order Confirmation'));
?>

<div class="order-confirmation-page container my-5"> <!-- Using Bootstrap container and margin utility -->

    <!-- Page Header -->
    <div class="page-header-component text-center mb-5">
        <h1 class="page-title display-4"><?= __('Thank You For Your Order!') ?></h1>
        <p class="page-description lead">
            <?= __('Your order has been placed successfully. You can find the details below and access your purchases in your dashboard.') ?>
        </p>
    </div>

    <?php if (isset($order) && $order): ?>
        <div class="order-details-section">

            <!-- Order Summary Card -->
            <div class="card order-summary-card mb-4 shadow-sm">
                <div class="card-header">
                    <h2 class="h5 mb-0"><?= __('Order Summary') ?></h2>
                </div>
                <div class="card-body p-3">
                    <div class="summary-item row mb-2">
                        <span class="summary-label col-sm-4 col-md-3 fw-bold"><?= __('Order Number:') ?></span>
                        <span class="summary-value col-sm-8 col-md-9"><?= h($order->id) ?></span>
                    </div>
                    <div class="summary-item row mb-2">
                        <span class="summary-label col-sm-4 col-md-3 fw-bold"><?= __('Order Date:') ?></span>
                        <span class="summary-value col-sm-8 col-md-9"><?= h($order->created->format('F jS, Y, H:i A')) ?></span>
                    </div>
                    <div class="summary-item row mb-2">
                        <span class="summary-label col-sm-4 col-md-3 fw-bold"><?= __('Subtotal:') ?></span>
                        <span class="summary-value col-sm-8 col-md-9"><?= $this->Number->currency($order->subtotal_amount) ?></span>
                    </div>
                    <?php if (isset($order->discount_amount) && $order->discount_amount > 0): ?>
                        <div class="summary-item row mb-2 discount-amount-item">
                            <span class="summary-label col-sm-4 col-md-3 fw-bold">
                                <?= __('Discount') ?>
                                <?php if (!empty($order->discount_code)): ?>
                                    (<?= h($order->discount_code) ?>)
                                <?php endif; ?>
                                :
                            </span>
                            <span class="summary-value col-sm-8 col-md-9 text-danger">- <?= $this->Number->currency($order->discount_amount) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="summary-item row mb-2 total-amount-item">
                        <span class="summary-label col-sm-4 col-md-3 fw-bold"><?= (isset($order->discount_amount) && $order->discount_amount > 0) ? __('Final Amount Charged:') : __('Total Amount:') ?></span>
                        <span class="summary-value col-sm-8 col-md-9 fw-bold text-success"><?= $this->Number->currency($order->total_amount) ?></span>
                    </div>
                </div>
            </div>

            <!-- Order Items Section -->
            <div class="order-items-container card shadow-sm mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0"><?= __('Items Purchased') ?></h2>
                </div>
                <div class="card-body p-3"> <!-- Changed p-0 to p-3 -->
                    <?php if (!empty($order->order_items)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($order->order_items as $item):
                                $itemTitle = $item->item_name ?? ($item->item_type === 'Course' && $item->course ? h($item->course->title) : ($item->item_type === 'Module' && $item->module ? h($item->module->title) : __('Item ID: {0}', $item->item_id)));
                                // Attempt to get an image URL if available (example logic)
                                $itemImageUrl = null;
                                if ($item->item_type === 'Course' && $item->course && !empty($item->course->image_url)) {
                                    $itemImageUrl = $item->course->image_url;
                                } elseif ($item->item_type === 'Module' && $item->module && !empty($item->module->image_url)) {
                                    $itemImageUrl = $item->module->image_url;
                                }
                            ?>
                                <li class="list-group-item order-item-card">
                                    <div class="row align-items-center">
                                        <?php if ($itemImageUrl): ?>
                                            <div class="col-md-2 col-sm-3 text-center item-image-container mb-2 mb-sm-0">
                                                <?= $this->Html->image($itemImageUrl, ['alt' => $itemTitle, 'class' => 'img-fluid rounded', 'style' => 'max-height: 75px; max-width: 100px; object-fit: cover;']) ?>
                                            </div>
                                            <div class="col-md-7 col-sm-9 item-details">
                                        <?php else: ?>
                                            <div class="col-md-9 col-sm-12 item-details">
                                        <?php endif; ?>
                                            <h6 class="item-title mb-1"><?= $itemTitle ?> <small class="text-muted">(<?= h($item->item_type) ?>)</small></h6>
                                            <p class="item-quantity text-muted small mb-0">Quantity: <?= h($item->quantity) ?></p>
                                        </div>
                                        <div class="col-md-3 col-sm-12 item-price mt-2 mt-sm-0"> <!-- Removed text-sm-end -->
                                            <span class="fw-bold"><?= $this->Number->currency($item->unit_price * $item->quantity) ?></span>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="p-3"><?= __('No items found for this order.') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Call to Action Buttons -->
            <div class="order-actions text-center mt-4">
                <?= $this->Html->link(__('Go to My Dashboard'), ['controller' => 'Dashboard', 'action' => 'index'], ['class' => 'btn btn-primary btn-lg me-2 button button-primary button-gold']) ?>
                <?= $this->Html->link(__('View Purchase History'), ['controller' => 'Dashboard', 'action' => 'purchaseHistory'], ['class' => 'btn btn-secondary btn-lg button button-secondary']) ?>
            </div>

        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center" role="alert">
            <h4 class="alert-heading"><?= __('Order Not Found')?></h4>
            <p><?= __('Could not retrieve order details. Please check your purchase history or contact support.') ?></p>
            <hr>
            <p class="mb-0"><?= $this->Html->link(__('Go to My Dashboard'), ['controller' => 'Dashboard', 'action' => 'index'], ['class' => 'btn btn-warning']) ?></p>
        </div>
    <?php endif; ?>
</div>