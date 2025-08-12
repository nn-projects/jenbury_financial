<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;


/*
 * This file is loaded in the context of the `Application` class.
 * So you can use `$this` to reference the application class instance
 * if required.
 */
return function (RouteBuilder $routes): void {
    /*
     * The default class to use for all routes
     *
     * The following route classes are supplied with CakePHP and are appropriate
     * to set as the default:
     *
     * - Route
     * - InflectedRoute
     * - DashedRoute
     *
     * If no call is made to `Router::defaultRouteClass()`, the class used is
     * `Route` (`Cake\Routing\Route\Route`)
     *
     * Note that `Route` does not do any inflections on URLs which will result in
     * inconsistently cased URLs when used with `{plugin}`, `{controller}` and
     * `{action}` markers.
     */
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {
        // Enable JSON extension parsing for this scope
        $builder->addExtensions(['json']);

        // Define the specific routes first
        $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
        $builder->connect('/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);
        $builder->connect('/reset-password/*', ['controller' => 'Users', 'action' => 'resetPassword']);
        $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
        $builder->connect('/courses', ['controller' => 'Courses', 'action' => 'index']);
        $builder->connect('/dashboard', ['controller' => 'Dashboard', 'action' => 'index']);
        $builder->connect('/dashboard/activity-feed', ['controller' => 'Dashboard', 'action' => 'activityFeed']); // Route for AJAX activity feed

        // Define route for Pages controller's URLs
        $builder->connect('/pages/*', 'Pages::display');

        // Define dynamic route for courses (ensure it comes before the catch-all)
        $builder->connect('/courses', ['controller' => 'Courses', 'action' => 'index']); // or 'index', depending on your route structure
        $builder->connect('/courses/purchase/{id}', ['controller' => 'Courses', 'action' => 'purchase'])->setPass(['id']);

        // ContentBlocks routes (replacing AdminController routes)
// ContentBlocks routes (replacing AdminController routes)
        $builder->connect('/ContentBlocks/index', ['controller' => 'ContentBlocks', 'action' => 'index']);
        $builder->connect('/ContentBlocks/restore/{id}', ['controller' => 'ContentBlocks', 'action' => 'restore'])
            ->setPass(['id']) // Pass the ID parameter to the action
            ->setPatterns(['id' => '\d+']); // Define pattern for ID (numeric)
        $builder->connect('/ContentBlocks/restore-all', ['controller' => 'ContentBlocks', 'action' => 'restoreAll']);
        $builder->connect('/ContentBlocks/edit/{id}', ['controller' => 'ContentBlocks', 'action' => 'edit'])
            ->setPass(['id'])
            ->setMethods(['GET', 'POST']); // Allow both GET and POST methods for editing

        // Route for updating module progress (AJAX)
        $builder->connect('/progress/update', ['controller' => 'Progress', 'action' => 'update'])
            ->setMethods(['POST']); // Ensure only POST requests match

        // Route for marking content complete (AJAX)
        $builder->connect('/progress/mark-content-complete', ['controller' => 'Progress', 'action' => 'markContentComplete'])
            ->setMethods(['POST']); // Ensure only POST requests match
// Route for applying discount code (AJAX)
        $builder->connect('/checkout/apply-discount', ['controller' => 'Checkout', 'action' => 'applyDiscount'])
            ->setMethods(['POST']); // Ensure only POST requests match

        // Consolidated Account Page Route
        $builder->connect('/users/account', ['controller' => 'Users', 'action' => 'account']);
        $builder->connect('/users/keep-alive', ['controller' => 'Users', 'action' => 'keepAlive']); // Keep-alive endpoint

        // Redirects from old account-related pages
        $builder->redirect('/users/profile', '/users/account', ['status' => 301]);
        $builder->redirect('/users/change-password', '/users/account#security', ['status' => 301]);
        $builder->redirect('/dashboard/purchase-history', '/users/account#history', ['status' => 301]);

        // Explicit route for Stripe Webhook (POST only)
        $builder->connect('/payments/webhook', ['controller' => 'Payments', 'action' => 'webhook'])
            ->setMethods(['POST']);

        // Fallback routes for other controllers
        $builder->fallbacks();
    });

    /*
     * Define routes for the Admin prefix /admin.
     */
    $routes->prefix('Admin', function (RouteBuilder $builder): void {
        // Enable JSON extension parsing for this scope if needed
        // $builder->addExtensions(['json']);

        // Connect dashboard route
        $builder->connect('/', ['controller' => 'Admin', 'action' => 'dashboard']); // Assuming AdminController::dashboard exists
// Explicitly connect /admin/dashboard to AdminController::dashboard
        $builder->connect('/dashboard', ['controller' => 'Admin', 'action' => 'dashboard']);

        // Connect resources for Discount Codes
        $builder->resources('DiscountCodes', [
            'actions' => ['index', 'add', 'edit', 'delete'] // Explicitly define actions if needed
        ]);
        // Add custom route for toggleStatus
        $builder->connect('/discount-codes/toggle-status/{id}', ['controller' => 'DiscountCodes', 'action' => 'toggleStatus'])
            ->setPass(['id'])
            ->setMethods(['POST']); // Use POST for actions that change state

        // Route for site content management
        $builder->connect('/site-content', ['controller' => 'Admin', 'action' => 'siteContent']);

        // Redirect /admin/users/account to a more appropriate admin page
        $builder->redirect('/users/account', ['controller' => 'Admin', 'action' => 'manageUsers'], ['status' => 301]);

        // Add other admin routes here...
        // Example: $builder->resources('Users');

        $builder->connect('/uploads/ckeditor_image', [
            'controller' => 'Uploads',
            'action' => 'ckeditorImage'
        ]);


        // Fallback route for admin controllers
        $builder->fallbacks();
    });
};