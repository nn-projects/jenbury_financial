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
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.3.4
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Error Handling Controller
 *
 * Controller used by ExceptionRenderer to render error responses.
 */
class ErrorController extends AppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        // Only add parent::initialize() if you are confident your appcontroller is safe.
    }

    /**
     * beforeFilter callback.
     *
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event Event.
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * beforeRender callback.
     *
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event Event.
     * @return \Cake\Http\Response|null|void
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        $this->viewBuilder()->setTemplatePath('Error');
    }

     /**
     * Handle the 400 error (Missing Controller)
     *
     * @return \Cake\Http\Response|null
     */
    public function error400()
    {
        $this->response = $this->response->withStatus(400); // Set the HTTP status code to 400
        $this->set('message', 'Bad Request: The page you requested could not be found.');
    }

    /**
     * Handle the 500 error (General Errors)
     *
     * @return \Cake\Http\Response|null
     */
    public function error500()
    {
        $this->response = $this->response->withStatus(500); // Set the HTTP status code to 500
        $this->set('message', 'Internal Server Error: Something went wrong on the server.');
    }
    
    /**
     * afterFilter callback.
     *
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event Event.
     * @return \Cake\Http\Response|null|void
     */
    public function afterFilter(EventInterface $event)
    {
    }
}
