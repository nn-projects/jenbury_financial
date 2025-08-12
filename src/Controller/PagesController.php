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

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;
use Cake\Validation\Validator; // Add this
use Cake\Mailer\Mailer; // Add this

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/5/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // Allow all pages to be accessed without authentication
        $this->Authentication->addUnauthenticatedActions(['display', 'home', 'about', 'contact', 'faq']);
    }
    
    /**
     * Home page
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function home()
    {
        // Load the Courses model to display featured courses
        $courses = $this->fetchTable('Courses');
        
        $featuredCourses = $courses->find()
            ->where(['is_active' => true])
            ->limit(3)
            ->all();
        
        $this->set(compact('featuredCourses'));
    }
    
    /**
     * About page
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function about()
    {
        // This will render the about.php template in templates/Pages/
    }
    
    /**
     * Contact page
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function contact()
    {
        if ($this->request->is('post')) {
            $validator = new Validator();
            $validator
                ->requirePresence('name', true, 'Please enter your name.')
                ->notEmptyString('name', 'Please enter your name.')
                ->maxLength('name', 100, 'Name cannot exceed 100 characters.')

                ->requirePresence('email', true, 'Please enter your email address.')
                ->notEmptyString('email', 'Please enter your email address.')
                ->add('email', 'validFormat', [
                    'rule' => 'email',
                    'message' => 'Please enter a valid email address.'
                ])

                ->requirePresence('subject', true, 'Please enter a subject.')
                ->notEmptyString('subject', 'Please enter a subject.')
                ->maxLength('subject', 150, 'Subject cannot exceed 150 characters.')

                ->requirePresence('message', true, 'Please enter your message.')
                ->notEmptyString('message', 'Please enter your message.')
                ->maxLength('message', 2000, 'Message cannot exceed 2000 characters.');

            $errors = $validator->validate($this->request->getData());

            if ($errors) {
                // Handle validation errors - display them back to the user
                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $this->Flash->error($error);
                    }
                }
            } else {
                // Validation passed - process the data (e.g., send email)
                $contactData = $this->request->getData();

                try {
                    $mailer = new Mailer('default'); // Use the 'default' mailer config
                    $mailer->setFrom([$contactData['email'] => $contactData['name']]) // Sender info
                           ->setTo(Configure::read('ContactForm.toEmail')) // Send to configured admin email
                           ->setReplyTo($contactData['email'], $contactData['name'])
                           ->setSubject('Contact Form Submission: ' . h($contactData['subject']))
                           ->deliver("Message from: " . h($contactData['name']) . "\n\n" . h($contactData['message']));

                    $this->Flash->success(__('Your message has been sent. We will get back to you soon.'));
                    return $this->redirect(['action' => 'contact']);

                } catch (\Exception $e) {
                    // Log the error
                    Log::error('Contact form email failed: ' . $e->getMessage());
                    $this->Flash->error(__('Unable to send your message at this time. Please try again later.'));
                }
            }
        }
    }
    
    /**
     * FAQ page
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function faq()
    {
        // This will render the faq.php template in templates/Pages/
    }

    /**
     * Displays a view
     *
     * @param string ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\View\Exception\MissingTemplateException When the view file could not
     *   be found and in debug mode.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found and not in debug mode.
     * @throws \Cake\View\Exception\MissingTemplateException In debug mode.
     */
    public function display(string ...$path): ?Response
    {
        if (!$path) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            return $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }

}
