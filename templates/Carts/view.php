<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cart|null $cart The cart entity, potentially containing cart_items with associated product details.
 * @var float $total The total price of the cart.
 */

$this->layout = 'jenbury'; // Or your default layout
$this->assign('title', __('Shopping Cart'));

// Link CSS and JS files
$this->Html->css('cart.css', ['block' => true]); // Add to head or specific block
$this->Html->script('cart.js', ['block' => true]); // Add before closing </body>

?>

<div class="cart-container">
    <div class="cart-header">
        <h1 class="cart-title"><?= __('Shopping Cart') ?></h1>
        <p class="cart-subtitle"><?= __('Review your items before proceeding to checkout') ?></p>
    </div>

    <?php // Check if cart exists and has items ?>
    <?php if ($cart && !empty($cart->cart_items)): ?>
        <div class="cart-grid">
            <div class="cart-items-column">
                <?php if (isset($suggestedCourse) && $suggestedCourse): ?>
                    <div class="callout callout-info" style="margin-bottom: 1rem; padding: 0.75rem 1.25rem; border: 1px solid #bee5eb; border-radius: 0.25rem; background-color: #d1ecf1; color: #0c5460;">
                        <p style="margin-bottom: 0;">
                            You have individual modules in your cart. Consider purchasing the full course '<?= $this->Html->link(h($suggestedCourse->title), ['controller' => 'Courses', 'action' => 'view', $suggestedCourse->id]) ?>'
                            for comprehensive learning and greater savings!
                            (Full course price: $<?= number_format($suggestedCourse->price, 2) ?>)
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (isset($replaceableCourseInfo) && $replaceableCourseInfo): ?>
                    <div class="callout callout-warning" style="margin-bottom: 1rem; padding: 0.75rem 1.25rem; border: 1px solid #ffecb5; border-radius: 0.25rem; background-color: #fff3cd; color: #856404;">
                        <p style="margin-bottom: 0.5rem;">
                            You have multiple modules from the course '<strong><?= h($replaceableCourseInfo['course_title']) ?></strong>' in your cart.
                        </p>
                        <?= $this->Form->create(null, ['url' => ['controller' => 'Carts', 'action' => 'replaceModulesWithCourse'], 'style' => 'margin-bottom: 0;']) ?>
                            <?= $this->Form->hidden('course_id_to_add', ['value' => $replaceableCourseInfo['course_id']]) ?>
                            <?php foreach ($replaceableCourseInfo['module_cart_item_ids'] as $cartItemId): ?>
                                <?= $this->Form->hidden('module_cart_item_ids[]', ['value' => $cartItemId]) ?>
                            <?php endforeach; ?>
                            <?= $this->Form->button(__('Upgrade to Full Course (Price: $%s)', number_format($replaceableCourseInfo['course_price'], 2)), [
                                'type' => 'submit',
                                'class' => 'button button-success', // Or another appropriate class
                                'title' => __('Remove current modules from this course and add the full course to cart')
                            ]) ?>
                        <?= $this->Form->end() ?>
                    </div>
                <?php endif; ?>

                <div class="cart-card cart-items-card">
                    <div class="cart-table-wrapper">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th class="cart-table-header item-col"><?= __('Item') ?></th>
                                    <th class="cart-table-header price-col"><?= __('Price') ?></th>
                                    <th class="cart-table-header actions-col"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart->cart_items as $item):
                                    // Access product details attached in the controller
                                    $productTitle = $item->product ? h($item->product->title) : __('Item Unavailable');
                                    $productType = h($item->item_type);
                                    // Use the price stored in the cart item
                                    $itemPrice = (float)$item->price;
                                    $itemTotal = $itemPrice; // Quantity is always 1

                                    // Generate Product URL
                                    $productUrl = '#'; // Default URL
                                    if ($item->product) {
                                        $productUrl = $item->item_type === 'Course'
                                            ? $this->Url->build(['controller' => 'Courses', 'action' => 'view', $item->product->slug ?? $item->item_id])
                                            : $this->Url->build(['controller' => 'Modules', 'action' => 'view', $item->item_id]);
                                    }
                                    $productInitial = $productTitle !== __('Item Unavailable') ? strtoupper(substr($productTitle, 0, 1)) : '?';
                                ?>
                                <tr class="cart-item-row">
                                    <td class="cart-table-cell item-cell" data-label="<?= __('Item') ?>">
                                        <div class="item-details">
                                            <div class="item-image-placeholder">
                                                <span><?= $productInitial ?></span>
                                            </div>
                                            <div class="item-info">
                                                <?php if ($item->product): ?>
                                                    <?= $this->Html->link($productTitle, $productUrl, ['class' => 'item-title-link']) ?>
                                                <?php else: ?>
                                                    <span class="item-title"><?= $productTitle ?></span>
                                                <?php endif; ?>
                                                <?php /* <p class="item-type"><?= $productType ?></p> */ ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cart-table-cell price-cell" data-label="<?= __('Price') ?>"><?= $this->Number->currency($itemPrice) ?></td>
                                    <td class="cart-table-cell actions-cell" data-label="<?= __('Actions') ?>">
                                        <?php // Keep postLink for now, JS will handle confirmation ?>
                                        <?= $this->Form->postLink(
                                            '<i class="fas fa-trash-alt"></i>', // Icon only
                                            ['controller' => 'Carts', 'action' => 'remove'],
                                            [
                                                'data' => ['cart_item_id' => $item->id],
                                                'confirm' => __('Are you sure you want to remove {0} from your cart?', $productTitle),
                                                'escapeTitle' => false,
                                                'class' => 'button button-icon button-danger remove-item-button', // Added class for JS targeting
                                                'title' => __('Remove {0}', $productTitle)
                                            ]
                                        ) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="cart-summary-column">
                <div class="cart-card cart-summary-card">
                    <div class="cart-card-header">
                        <h5 class="cart-card-title"><?= __('Cart Summary') ?></h5>
                    </div>
                    <div class="cart-card-body">
                        <div class="summary-line">
                            <span><?= __('Subtotal:') ?></span>
                            <strong><?= $this->Number->currency($total) ?></strong>
                        </div>
                        <!-- Add tax/shipping if applicable -->
                        <hr class="summary-separator">
                        <div class="summary-line summary-total">
                            <span><?= __('Total:') ?></span>
                            <strong><?= $this->Number->currency($total) ?></strong>
                        </div>
                    </div>
                    <div class="cart-card-footer">
                        <?= $this->Html->link(__('Proceed to Checkout'), ['controller' => 'Checkout', 'action' => 'index'], ['class' => 'button button-primary button-full-width']) ?>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="cart-empty-message">
            <p><?= __('Your shopping cart is empty.') ?> <?= $this->Html->link(__('Continue Shopping'), ['controller' => 'Courses', 'action' => 'index'], ['class' => 'link']) ?></p>
        </div>
    <?php endif; ?>
</div>