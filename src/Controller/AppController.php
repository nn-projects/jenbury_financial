<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry; // Add this for table access

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/5/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('FormProtection', [
            'unlockedActions' => ['login', 'editCourse', 'editLessonContent'], // Unlock editLessonContent as well
            'unlockedFields' => ['image_file', 'file_upload'] // Also unlock the likely field name 'file_upload' used here
        ]);

        // Set the default layout to our custom layout
        $this->viewBuilder()->setLayout('jenbury');
        
        // Share common variables with all views
        
        $this->set('loggedIn', $this->Authentication->getIdentity() !== null);
        $this->set('currentUser', $this->Authentication->getIdentity());
    }
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        Log::debug('AppController beforeFilter reached for URL: ' . $this->request->getPath());
    
        // Get the current URL path
        $currentUrl = $this->request->getPath();
    
        // Check if the current URL starts with "/content-blocks"
        if (strpos($currentUrl, '/content-blocks') === 0) {
            // Restrict access and redirect to error page
            $this->Flash->error(__('You are not authorized to access this page.'));
            return $this->redirect('/error/error400');  // Redirect to custom error page
        }
    }
    
    /**
     * Before render callback.
     *
     * @param \Cake\Event\EventInterface $event The beforeRender event.
     * @return void
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);
    
        // Check URL to prevent rendering on restricted paths
        $currentUrl = $this->request->getPath();
    
        // Set response status to forbidden (403) for access attempts
        if (strpos($currentUrl, '/content-blocks') === 0) {
            // Set response status to forbidden (400)
            $this->response = $this->response->withStatus(400); // Forbidden status
            $this->Flash->error(__('You are not authorized to access this page.'));
            // Avoid redundant redirection; set status and flash message, then allow the error page to handle it
            return $this->redirect('/error/error400');
        }
    
        // For all views, pass the site name
        $this->set('siteName', 'Jenbury Financial');


    }

}


