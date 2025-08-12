<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use Cake\Routing\Router;

/**
 * Carts Controller
 *
 * Handles shopping cart operations.
 *
 * @property \App\Model\Table\CartsTable $Carts
 * @property \App\Model\Table\CartItemsTable $CartItems
 * @property \App\Model\Table\CoursesTable $Courses
 * @property \App\Model\Table\ModulesTable $Modules
 */
class CartsController extends AppController
{
    /**
     * Initialization hook method.
     *
     * @param \Cake\Event\EventInterface $event The event instance.
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // Actions that require authentication
        $this->Authentication->allowUnauthenticated(['view', 'add', 'update', 'remove']); // Allow guests for now, adjust as needed
        // Models are loaded via PHPDoc annotations and magic __get
    }

    /**
     * Add method
     *
     * Adds an item to the cart. Expects 'item_id', 'item_type', 'quantity' in POST data.
     *
     * @return \Cake\Http\Response|null|void Redirects or returns JSON.
     */
    public function add()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->Authentication->getIdentity()?->getIdentifier();

        if (!$userId) {
            // Handle guest cart logic here (e.g., using session) - Placeholder
            $this->Flash->info(__('Please log in to add items to your cart.'));
            return $this->redirect($this->referer(['action' => 'view']));
            // For now, we require login. Implement session cart later if needed.
            // throw new ForbiddenException('Login required.');
        }

        $itemId = $this->request->getData('item_id');
        $itemType = $this->request->getData('item_type');
        $quantity = (int)$this->request->getData('quantity', 1);

        if (!$itemId || !$itemType || !in_array($itemType, ['Course', 'Module']) || $quantity <= 0) {
            throw new BadRequestException(__('Invalid item data provided.'));
        }
        $itemId = (int)$itemId; // Ensure item ID is integer

        // Check if item exists and get its price
        $itemPrice = 0.0;
        $itemName = 'Unknown Item';
        $itemTable = null; // Initialize item table variable
        if ($itemType === 'Course') {
            $itemTable = $this->fetchTable('Courses');
            $item = $itemTable->findById($itemId)->first();
            if (!$item) throw new NotFoundException(__('Course not found.'));
            $itemPrice = (float)$item->get('price');
            $itemName = $item->get('title');
        } elseif ($itemType === 'Module') {
            $itemTable = $this->fetchTable('Modules');
            $item = $itemTable->findById($itemId)->first();
            if (!$item) throw new NotFoundException(__('Module not found.'));
            $itemPrice = (float)$item->get('price');
            $itemName = $item->get('title');
        } else {
             // This case should ideally not be reached due to the check on line 62,
             // but adding for robustness.
             throw new BadRequestException(__('Invalid item type specified.'));
        }

        $cartItemsTable = $this->fetchTable('CartItems'); // Load CartItems table

        // Check if user already owns the item
        if ($cartItemsTable->isItemOwnedByUser($userId, $itemId, $itemType)) {
            $this->Flash->info(__('You already own "{0}".', $itemName));
            return $this->redirect(['action' => 'view']); // Redirect consistently to cart view
        }

        // Find or create the user's cart
        $cart = $this->Carts->findOrCreate(['user_id' => $userId]);

        // Check if item already exists in the cart
        $cartItem = $cartItemsTable->find()
            ->where([
                'cart_id' => $cart->id,
                'item_id' => $itemId,
                'item_type' => $itemType,
            ])
            ->first();

        if ($cartItem) {
            // Item already exists, show info message and redirect
            $this->Flash->info(__('This item is already in your cart.'));
            return $this->redirect(['action' => 'view']);
        } else {
            // Create new cart item
            $cartItem = $cartItemsTable->newEntity([
                'cart_id' => $cart->id,
                'item_id' => $itemId,
                'item_type' => $itemType,
                'quantity' => 1, // Enforce quantity 1
                'price' => $itemPrice, // Store price at time of adding
            ]);
        }

        if ($cartItemsTable->save($cartItem)) {
            $this->Flash->success(__('{0} has been added to your cart.', $itemName));
        } else {
            Log::error('Failed to save cart item: ' . print_r($cartItem->getErrors(), true), ['scope' => ['cart']]);
            $this->Flash->error(__('Unable to add item to cart. Please try again.'));
        }

        // TODO: Implement JSON response for AJAX requests if needed
        return $this->redirect(['action' => 'view']);
    }

    /**
     * Update method
     *
     * Updates item quantity in the cart. Expects 'cart_item_id', 'quantity' in POST data.
     *
     * @return \Cake\Http\Response|null|void Redirects or returns JSON.
     */
    /* // Commenting out update action as quantity is fixed at 1
    public function update()
    {
        $this->request->allowMethod(['post', 'put']);
        $userId = $this->Authentication->getIdentity()?->getIdentifier();

        if (!$userId) {
            // Handle guest cart logic here - Placeholder
            $this->Flash->error(__('Login is required to update the cart. Guest carts not yet implemented.'));
            return $this->redirect(['action' => 'view']);
            // throw new ForbiddenException('Login required.');
        }

        $cartItemId = (int)$this->request->getData('cart_item_id');
        $quantity = (int)$this->request->getData('quantity');

        if (!$cartItemId) {
             throw new BadRequestException(__('Invalid cart item ID provided.'));
        }

        /** @var \App\Model\Entity\CartItem $cartItem * /
        $cartItem = $this->CartItems->find()
            ->contain(['Carts']) // Contain Carts to check user_id
            ->where(['CartItems.id' => $cartItemId])
            ->first();

        if (!$cartItem) {
            throw new NotFoundException(__('Cart item not found.'));
        }

        // Security check: Ensure the item belongs to the current user's cart
        if ($cartItem->cart->user_id !== $userId) {
            throw new ForbiddenException(__('You are not authorized to modify this cart item.'));
        }

        if ($quantity <= 0) {
            // Remove item if quantity is zero or less
            if ($this->CartItems->delete($cartItem)) {
                $this->Flash->success(__('Item removed from cart.'));
            } else {
                Log::error('Failed to delete cart item: ' . $cartItemId, ['scope' => ['cart']]);
                $this->Flash->error(__('Unable to remove item from cart.'));
            }
        } else {
            // Update quantity
            $cartItem->quantity = $quantity;
            if ($this->CartItems->save($cartItem)) {
                $this->Flash->success(__('Cart updated.'));
            } else {
                Log::error('Failed to update cart item: ' . print_r($cartItem->getErrors(), true), ['scope' => ['cart']]);
                $this->Flash->error(__('Unable to update cart. Please try again.'));
            }
        }

        // TODO: Implement JSON response for AJAX requests if needed
        return $this->redirect(['action' => 'view']);
    }
    */

    /**
     * Remove method
     *
     * Removes an item from the cart. Expects 'cart_item_id' in POST data.
     *
     * @return \Cake\Http\Response|null|void Redirects or returns JSON.
     */
    public function remove()
    {
        // Log::debug('CartsController::remove - Action started.', ['scope' => ['cart_debug']]); // Remove Log
        $this->request->allowMethod(['post', 'delete']);
        $userId = $this->Authentication->getIdentity()?->getIdentifier();
        // Log::debug('CartsController::remove - User ID: ' . ($userId ?? 'NULL'), ['scope' => ['cart_debug']]); // Remove Log

         if (!$userId) {
            // Handle guest cart logic here - Placeholder
            // Log::warning('CartsController::remove - User not logged in.', ['scope' => ['cart_debug']]); // Remove Log
            $this->Flash->info(__('Please log in to manage your cart.'));
            return $this->redirect(['action' => 'view']);
            // throw new ForbiddenException('Login required.');
        }

        $cartItemId = (int)$this->request->getData('cart_item_id');
        // Log::debug('CartsController::remove - Received Cart Item ID: ' . $cartItemId, ['scope' => ['cart_debug']]); // Remove Log

        if (!$cartItemId) {
             // Log::error('CartsController::remove - Invalid Cart Item ID received.', ['scope' => ['cart_debug']]); // Remove Log
             throw new BadRequestException(__('Invalid cart item ID provided.'));
        }

        /** @var \App\Model\Entity\CartItem $cartItem */
        // Log::debug('CartsController::remove - Attempting to find CartItem ID: ' . $cartItemId, ['scope' => ['cart_debug']]); // Remove Log
        $cartItemsTable = $this->fetchTable('CartItems'); // Explicitly load the table
        $cartItem = $cartItemsTable->find()
            ->contain(['Carts']) // Contain Carts to check user_id
            ->where(['CartItems.id' => $cartItemId])
            ->first();

        if (!$cartItem) {
            // Log::error('CartsController::remove - CartItem not found for ID: ' . $cartItemId, ['scope' => ['cart_debug']]); // Remove Log
            throw new NotFoundException(__('Cart item not found.'));
        }
        // Log::debug('CartsController::remove - Found CartItem: ' . print_r($cartItem->toArray(), true), ['scope' => ['cart_debug']]); // Remove Log


        // Security check: Ensure the item belongs to the current user's cart
        if ($cartItem->cart->user_id !== $userId) {
            // Log::warning('CartsController::remove - Authorization failed. Item User ID: ' . $cartItem->cart->user_id . ', Current User ID: ' . $userId, ['scope' => ['cart_debug']]); // Remove Log
            throw new ForbiddenException(__('You are not authorized to remove this cart item.'));
        }
        // Log::debug('CartsController::remove - Authorization successful.', ['scope' => ['cart_debug']]); // Remove Log

        // Log::debug('CartsController::remove - Attempting to delete CartItem ID: ' . $cartItemId, ['scope' => ['cart_debug']]); // Remove Log
        // Use the explicitly loaded table variable here too
        $deleteResult = $cartItemsTable->delete($cartItem); // Store result
        // Log::debug('CartsController::remove - Delete result: ' . ($deleteResult ? 'Success' : 'Failure'), ['scope' => ['cart_debug']]); // Remove Log

        if ($deleteResult) {
            $this->Flash->success(__('Item removed from cart.'));
        } else {
            Log::error('Failed to delete cart item: ' . $cartItemId, ['scope' => ['cart']]); // Keep original error log
            $this->Flash->error(__('Unable to remove item from cart. Please try again.'));
        }

        // TODO: Implement JSON response for AJAX requests if needed
        return $this->redirect(['action' => 'view']);
    }

    /**
     * View method
     *
     * Displays the current cart contents.
     *
     * @return \Cake\Http\Response|null|void Renders view.
     */
    public function view()
    {
        $userId = $this->Authentication->getIdentity()?->getIdentifier();
        $cart = null;
        $total = 0.0;
        $suggestedCourse = null; // Initialize suggestedCourse
        $replaceableCourseInfo = null; // Initialize for "Replace with Full Course"

        if ($userId) {
            // Fetch cart for logged-in user
            $cart = $this->Carts->find()
                ->where(['Carts.user_id' => $userId])
                ->contain([
                    'CartItems' // Eager load basic cart items
                ])
                ->first();

            if ($cart && $cart->cart_items) {
                // Fetch associated Course/Module details for display
                $coursesTable = $this->fetchTable('Courses');
                $modulesTable = $this->fetchTable('Modules');

                $onlyModulesInCart = true;
                $firstModuleParentCourseId = null;
                $allModulesBelongToSameCourse = true;

                foreach ($cart->cart_items as &$item) {
                    $product = null;
                    if ($item->item_type === 'Course') {
                        $product = $coursesTable->findById($item->item_id)->select(['id', 'title', 'price'])->first();
                        $onlyModulesInCart = false;
                        $allModulesBelongToSameCourse = false; // Cart has a course, suggestion not needed based on modules
                    } elseif ($item->item_type === 'Module') {
                        // Fetch module with its course to check parent course ID
                        $moduleWithCourse = $modulesTable->get($item->item_id, [
                            'contain' => ['Courses' => ['fields' => ['id', 'title', 'price']]]
                        ]);
                        $product = $moduleWithCourse; // Product for the view is the module entity

                        if ($onlyModulesInCart) { // Only process this logic if we haven't found a course yet
                            if (!$moduleWithCourse->course || !$moduleWithCourse->course_id) {
                                $allModulesBelongToSameCourse = false; // Module not linked to a course
                            } else {
                                if ($firstModuleParentCourseId === null) {
                                    $firstModuleParentCourseId = $moduleWithCourse->course_id;
                                } elseif ($moduleWithCourse->course_id !== $firstModuleParentCourseId) {
                                    $allModulesBelongToSameCourse = false; // Modules from different courses
                                }
                            }
                        }
                    }
                    $item->product = $product; // Attach fetched product (Course or Module entity)
                }
                unset($item); // Unset reference

                if ($onlyModulesInCart && $allModulesBelongToSameCourse && $firstModuleParentCourseId !== null) {
                    // This logic is for the general suggestion message.
                    // We might refine this or let the new $replaceableCourseInfo take precedence for the button.
                    if (!$suggestedCourse) { // Only set if not already set by a more specific rule
                        $suggestedCourse = $coursesTable->get($firstModuleParentCourseId);
                    }
                }

                // Logic for "Replace with Full Course" button
                $modulesInCartByCourse = [];
                $courseAlreadyInCart = false;
                if ($cart && $cart->cart_items) {
                    foreach ($cart->cart_items as $item) {
                        if ($item->item_type === 'Module' && isset($item->product->course_id)) {
                            $modulesInCartByCourse[$item->product->course_id][] = $item->id; // Store CartItem ID
                        } elseif ($item->item_type === 'Course') {
                            // Check if any of the potential replacement courses are already in cart
                            if (isset($modulesInCartByCourse[$item->item_id])) {
                                $courseAlreadyInCart = true; // A parent course of grouped modules is already in cart
                            }
                        }
                    }

                    if (!$courseAlreadyInCart) { // Only proceed if the target course isn't already there
                        foreach ($modulesInCartByCourse as $courseId => $moduleCartItemIds) {
                            if (count($moduleCartItemIds) > 1) { // More than one module from the same course
                                $parentCourse = $coursesTable->get($courseId);
                                if ($parentCourse) {
                                    $replaceableCourseInfo = [
                                        'course_id' => $parentCourse->id,
                                        'course_title' => $parentCourse->title,
                                        'course_price' => $parentCourse->price,
                                        'module_cart_item_ids' => $moduleCartItemIds,
                                    ];
                                    // For simplicity, suggest replacing for the first course found with multiple modules
                                    break;
                                }
                            }
                        }
                    }
                }
                // End of "Replace with Full Course" logic

                $total = $this->Carts->calculateTotal($cart->id);
            }
        } else {
            // Handle guest cart logic (e.g., from session) - Placeholder
            $loginUrl = Router::url(['controller' => 'Users', 'action' => 'login']);
            $messageText = __('Log in or create an account to start adding modules to your cart and begin your financial journey!');
            $linkifiedMessage = sprintf('<a href="%s" style="text-decoration: none; color: inherit;">%s</a>', $loginUrl, $messageText);
            $this->Flash->info($linkifiedMessage, ['escape' => false]);
            // Potentially load cart from session here
        }

        // Items were enriched directly in the loop above

        $this->set(compact('cart', 'total', 'suggestedCourse', 'replaceableCourseInfo'));
    }

    /**
     * Replace Modules With Course method
     *
     * Removes specified module cart items and adds their parent course to the cart.
     * Expects 'course_id_to_add' and 'module_cart_item_ids' (array) in POST data.
     *
     * @return \Cake\Http\Response|null|void Redirects to cart view.
     */
    public function replaceModulesWithCourse()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->Authentication->getIdentity()?->getIdentifier();

        if (!$userId) {
            $this->Flash->error(__('Please log in to modify your cart.'));
            return $this->redirect(['action' => 'view']);
        }

        $courseIdToAdd = (int)$this->request->getData('course_id_to_add');
        $moduleCartItemIdsToRemove = (array)$this->request->getData('module_cart_item_ids');

        if (!$courseIdToAdd || empty($moduleCartItemIdsToRemove)) {
            $this->Flash->error(__('Invalid data provided for replacing modules with course.'));
            return $this->redirect(['action' => 'view']);
        }

        $cartItemsTable = $this->fetchTable('CartItems');
        $coursesTable = $this->fetchTable('Courses');
        $cart = $this->Carts->findOrCreate(['user_id' => $userId]);

        // 1. Remove the specified module cart items
        $modulesRemovedCount = 0;
        foreach ($moduleCartItemIdsToRemove as $cartItemId) {
            $cartItem = $cartItemsTable->find()
                ->where(['id' => (int)$cartItemId, 'cart_id' => $cart->id])
                ->first();

            if ($cartItem) {
                if ($cartItemsTable->delete($cartItem)) {
                    $modulesRemovedCount++;
                } else {
                    Log::error(__('Failed to remove module cart item ID: {0} during course replacement.', $cartItemId), ['scope' => ['cart']]);
                }
            }
        }

        // 2. Add the full course to the cart
        $course = $coursesTable->findById($courseIdToAdd)->first();
        if (!$course) {
            $this->Flash->error(__('The selected course could not be found.'));
            return $this->redirect(['action' => 'view']);
        }

        // Check if course is already owned or already in cart (after module removal)
        if ($cartItemsTable->isItemOwnedByUser($userId, $course->id, 'Course')) {
            $this->Flash->info(__('You already own "{0}".', h($course->title)));
            return $this->redirect(['action' => 'view']);
        }

        $existingCourseCartItem = $cartItemsTable->find()
            ->where(['cart_id' => $cart->id, 'item_id' => $course->id, 'item_type' => 'Course'])
            ->first();

        if ($existingCourseCartItem) {
            $this->Flash->info(__('{0} is already in your cart.', h($course->title)));
        } else {
            $newCourseCartItem = $cartItemsTable->newEntity([
                'cart_id' => $cart->id,
                'item_id' => $course->id,
                'item_type' => 'Course',
                'quantity' => 1,
                'price' => (float)$course->price,
            ]);
            if ($cartItemsTable->save($newCourseCartItem)) {
                $this->Flash->success(__('{0} has been added to your cart, and {1} module(s) were removed.', h($course->title), $modulesRemovedCount));
            } else {
                Log::error(__('Failed to add course {0} to cart after removing modules. Errors: {1}', $course->title, print_r($newCourseCartItem->getErrors(), true)), ['scope' => ['cart']]);
                $this->Flash->error(__('Could not add {0} to your cart. Please try again.', h($course->title)));
            }
        }

        return $this->redirect(['action' => 'view']);
    }
}