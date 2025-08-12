<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Log\Log; // Added for logging
use Cake\Utility\Text;
use Cake\Mailer\Mailer;
use Cake\Http\Client;
use Cake\Core\Configure;
use Authentication\PasswordHasher\DefaultPasswordHasher; // Already present
use Cake\I18n\FrozenTime;
/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\PurchasesTable $Purchases // Added for type hinting
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        // Only parent initialize needed here. Components loaded in actions if required.
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Authentication->addUnauthenticatedActions(['login', 'register', 'forgotPassword', 'resetPassword']);
    }

    /**
     * Login method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $result = $this->Authentication->getResult();
            if ($result->isValid()) {
                $user = $this->Authentication->getIdentity();


                // Role-based redirect
                if ($user->role === 'admin') {
                    return $this->redirect(['controller' => 'Admin', 'action' => 'dashboard']);
                }

                // Default redirect for non-admin users
                return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
            }

            $this->Flash->error(__('Invalid username or password'));
        } elseif ($this->Authentication->getResult()?->isValid()) {
            $user = $this->Authentication->getIdentity();

            // Check again for role-based redirect in GET request scenario
            if ($user->role === 'admin') {
                return $this->redirect(['controller' => 'Admin', 'action' => 'dashboard']);
            }

            return $this->redirect($this->Authentication->getLoginRedirect() ?? '/dashboard');
        }

        // If this is a GET request or login failed, render the login form
        $this->viewBuilder()->setLayout('jenbury');
    }


    /**
     * Register method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function register()
    {
        // Allow the g-recaptcha-response field to bypass form protection
        $this->FormProtection->setConfig('unlockedFields', ['g-recaptcha-response']);

        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $userData = $this->request->getData();
            // Set default role to 'member'
            $userData['role'] = 'member';

            $recaptchaResponse = $this->request->getData('g-recaptcha-response');
            $secretKey = Configure::read('Recaptcha.secretKey');

            if (empty($recaptchaResponse)) {
                $this->Flash->error(__('Please complete the reCAPTCHA.'));
            } else {
                // Verify reCAPTCHA
                $http = new Client();
                $response = $http->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $recaptchaResponse,
                ]);

                $result = $response->getJson();

                // Check if the reCAPTCHA validation was successful
                if (!$result['success']) {
                    $this->Flash->error(__('reCAPTCHA verification failed. Please try again.'));
                } else {
                    // Proceed with user registration
                    $user = $this->Users->patchEntity($user, $userData);

                    if ($user->getErrors()) {
                        foreach ($user->getErrors() as $field => $errors) {
                            foreach ($errors as $error) {
                                $this->Flash->error($error);
                            }
                        }
                        return $this->render();
                    }

                    if ($this->Users->save($user)) {
                        $this->Flash->success(__('Your account has been created. Please log in.'));
                        return $this->redirect(['action' => 'login']);
                    }
                    $this->Flash->error(__('The user could not be saved. Please, try again.'));
                }
            }
        }
        if (empty($user)) {
            $user = $this->Users->newEmptyEntity();
        }
        $this->set(compact('user'));
    }

    /**
     * Logout method
     *
     * @return \Cake\Http\Response|null|void Redirects to homepage.
     */
    public function logout()
    {
        $result = $this->Authentication->getResult();
        // Regardless of POST or GET, redirect if user is logged in
        if ($result && $result->isValid()) {
            $this->Authentication->logout();
            return $this->redirect('/');
        }
    }

    /**
     * Consolidated Account Management method
     * Handles Profile Update, Password Change, and displays Purchase History.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function account()
    {
        Log::debug('UsersController::account() reached. Request URL: ' . $this->request->getRequestTarget());
        // Paginator component is not needed in CakePHP 5; $this->paginate() is available directly.

        $userId = $this->Authentication->getIdentity()->getIdentifier();
        $user = $this->Users->get($userId); // No need for contain here initially

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Determine if it's a profile update or password change
            if (isset($data['current_password'])) {
                // --- Password Change Logic ---
                $hasher = new DefaultPasswordHasher();
                if (!$hasher->check($data['current_password'], $user->password)) {
                    $this->Flash->error(__('Current password incorrect.'));
                    // Don't redirect here, let the page re-render with the error
                } elseif ($data['new_password'] !== $data['confirm_password']) {
                    $this->Flash->error(__('New passwords do not match.'));
                    // Don't redirect here
                } else {
                    // Attempt to save the new password
                    $user = $this->Users->patchEntity($user, ['password' => $data['new_password']]);
                    if ($this->Users->save($user)) {
                        $this->Flash->success(__('Your password has been updated successfully.'));
                        return $this->redirect(['action' => 'account']); // Redirect on success
                    } else {
                        // Extract and display validation errors for password
                        $errors = $user->getErrors();
                        $errorMsg = 'Unable to update your password. Please correct the following issues:<ul>';
                        array_walk_recursive($errors, function ($message) use (&$errorMsg) {
                            $errorMsg .= '<li>' . h($message) . '</li>';
                        });
                        $errorMsg .= '</ul>';
                        $this->Flash->error($errorMsg, ['params' => ['escape' => false]]);
                    }
                }
            } else {
                // --- Profile Update Logic ---
                $user = $this->Users->patchEntity($user, $data, [
                    'fieldList' => ['first_name', 'last_name', 'email']
                ]);
                if ($this->Users->save($user)) {
                    // Update the session identity
                    $this->Authentication->setIdentity($user);
                    $this->Flash->success(__('Your profile has been updated successfully.'));
                    return $this->redirect(['action' => 'account']); // Redirect on success
                } else {
                    $this->Flash->error(__('Unable to update your profile. Please check the form for errors.'));
                     // Extract and display validation errors for profile (optional, but good practice)
                    $errors = $user->getErrors();
                    if (!empty($errors)) {
                        $errorMsg = 'Please correct the following issues:<ul>';
                        array_walk_recursive($errors, function ($message) use (&$errorMsg) {
                            $errorMsg .= '<li>' . h($message) . '</li>';
                        });
                        $errorMsg .= '</ul>';
                        // Append to the generic error message or replace it
                        $this->Flash->error($errorMsg, ['params' => ['escape' => false]]);
                    }
                }
            }
             // Re-fetch user data if save failed to ensure view has latest (even if invalid) data
             $user = $this->Users->get($userId);
        }

        // Fetch Purchase History (Paginated)
        $purchasesQuery = $this->fetchTable('Purchases')->find() // Use fetchTable() to load the model
            ->where(['Purchases.user_id' => $userId])
            ->contain([
                'Courses',
                'Modules',
                'Orders' => [ // Eager load the associated Order
                    'OrderItems' // And its OrderItems
                ]
            ]); // Assuming relationships are set up
            // Removed default orderBy - let paginate handle sorting based on URL params or its defaults

        $purchases = $this->paginate($purchasesQuery, [
            'sortableFields' => [
                'Purchases.id', 'Purchases.created', 'Purchases.amount', 'Purchases.payment_status'
                // Also allow sorting by related fields if needed in the future, e.g.:
                // 'Courses.title', 'Modules.title'
            ]
        ]);

        $this->set(compact('user', 'purchases'));
    }

    /**
     * Profile method - DEPRECATED (Functionality moved to account())
     * Kept temporarily for potential redirects or reference, should be removed later.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function profile()
    {
         // Redirect to the new consolidated account page
         return $this->redirect(['action' => 'account']);

        // --- Original Logic (Commented out) ---
        // $user = $this->Authentication->getIdentity();
        // $userId = $user->getIdentifier();
        // $user = $this->Users->get($userId, contain: []);
        //
        // if ($this->request->is(['patch', 'post', 'put'])) {
        //     $user = $this->Users->patchEntity($user, $this->request->getData(), [
        //         'fieldList' => ['first_name', 'last_name', 'email']
        //         // Note: 'role' is intentionally excluded to prevent users from changing their role
        //     ]);
        //     if ($this->Users->save($user)) {
        //         // Update the session identity with the new user data
        //         $this->Authentication->setIdentity($user);
        //         $this->Flash->success(__('Your profile has been updated.'));
        //         return $this->redirect(['action' => 'profile']);
        //     }
        //     $this->Flash->error(__('Unable to update your profile.'));
        // }
        //
        // $this->set(compact('user'));
    }

    /**
     * Change Password method - DEPRECATED (Functionality moved to account())
     * Kept temporarily for potential redirects or reference, should be removed later.
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function changePassword()
    {
        // Redirect to the new consolidated account page
        return $this->redirect(['action' => 'account', '#' => 'security']); // Add hash for convenience

        // --- Original Logic (Commented out) ---
        // $user = $this->Authentication->getIdentity();
        // $userId = $user->getIdentifier();
        // $user = $this->Users->get($userId, contain: []);
        //
        // if ($this->request->is(['patch', 'post', 'put'])) {
        //     $data = $this->request->getData();
        //
        //     $hasher = new DefaultPasswordHasher();
        //     if (!$hasher->check($data['current_password'], $user->password)) {
        //          $this->Flash->error(__('Current password incorrect.')); // Added Flash message
        //         return $this->redirect(['action' => 'changePassword']);
        //     }
        //
        //     // Verify new passwords match
        //     if ($data['new_password'] !== $data['confirm_password']) {
        //         $this->Flash->error(__('New passwords do not match.'));
        //         return $this->redirect(['action' => 'changePassword']);
        //     }
        //
        //     $user = $this->Users->patchEntity($user, [
        //         'password' => $data['new_password']
        //     ]);
        //
        //     if ($this->Users->save($user)) {
        //         $this->Flash->success(__('Your password has been updated.'));
        //         return $this->redirect(['action' => 'profile']);
        //     } else {
        //         // Extract and display validation errors
        //         $errors = $user->getErrors();
        //         $errorMsg = 'Unable to update your password. Please correct the following issues:<ul>';
        //         // Flatten the errors array and loop through messages
        //         array_walk_recursive($errors, function ($message) use (&$errorMsg) {
        //             $errorMsg .= '<li>' . h($message) . '</li>';
        //         });
        //         $errorMsg .= '</ul>';
        //         // Use allowHtml (via escape=>false) to render the list correctly
        //         $this->Flash->error($errorMsg, ['params' => ['escape' => false]]);
        //     }
        // }
        //
        // $this->set(compact('user'));
    }

    /**
     * Forgot Password method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    /**
     * Forgot Password method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function forgotPassword()
    {
        if ($this->request->is('post')) {
            // Log the email received in the POST data
            $email = $this->request->getData('email');
            $this->log('Password reset requested for email: ' . $email, 'debug');

            // Retrieve the user entity by provided email address
            $user = $this->Users->findByEmail($email)->first();

            if ($user) {
                // Log user found
                $this->log('User found for email: ' . $email, 'debug');

                // Set nonce and expiry date
                $user->nonce = Text::uuid();
                $user->nonce_expiry = (new \DateTime())->modify('+10 minutes');

                // Log nonce and expiry date being set
                $this->log('Nonce set for user: ' . $user->nonce, 'debug');
                $this->log('Nonce expiry set for user: ' . $user->nonce_expiry->format('Y-m-d H:i:s'), 'debug');

                if ($this->Users->save($user)) {
                    // Log success in saving user
                    $this->log('User nonce and expiry saved successfully.', 'debug');

                    // Now let's send the password reset email
                    $mailer = new Mailer();

                    // Setup the email configuration
                    $mailer->setEmailFormat('both')
                        ->setTo($user->email)
                        ->setSubject('Reset your account password')
                        ->viewBuilder()
                        ->setTemplate('resetpassword'); // Assuming you have a reset_password email template

                    // Set variables for the email template
                    $mailer->setViewVars([
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'nonce' => $user->nonce,
                        'email' => $user->email,
                    ]);

                    // Send the email
                    try {
                        $email_result = $mailer->deliver();
                        $this->log('Password reset email sent to: ' . $user->email, 'debug');
                    } catch (\Exception $e) {
                        // Log email delivery exception
                        $this->log('Email delivery exception: ' . $e->getMessage(), 'error');
                    }
                } else {
                    // Log error if saving user failed
                    $this->log('Error saving nonce and expiry for user: ' . $email, 'debug');
                    // Just in case something goes wrong when saving nonce and expiry
                    $this->Flash->error('We are having issue to reset your password. Please try again.');
                    return $this->render();
                }
            } else {
                // Log that the user was not found
                $this->log('No user found for email: ' . $email, 'debug');
            }

            // We don't reveal whether the email exists or not for security reasons
            $this->Flash->success('Please check your inbox (or spam folder) for an email regarding how to reset your account password.');
            return $this->redirect(['action' => 'login']);
        }
    }

    /**
     * Reset Password method
     *
     * @param string|null $nonce Reset password nonce
     * @return \Cake\Http\Response|null|void Redirects on successful password reset, renders view otherwise.
     */
    public function resetPassword(?string $nonce = null)
    {
        // Find the user by the nonce
        $user = $this->Users->findByNonce($nonce)->first();

        // If nonce is invalid or expired, set $user to null and redirect
        if (!$user || $user->nonce_expiry < new \DateTime()) {
            $user = null; // Ensure $user is defined
            $this->Flash->error('Your link is invalid or expired. Please try again.');
            return $this->redirect(['action' => 'forgotPassword']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            // Verify reCAPTCHA
            $recaptchaResponse = $this->request->getData('g-recaptcha-response');
            $secretKey = Configure::read('Recaptcha.secretKey');

            if (empty($recaptchaResponse)) {
                $this->Flash->error(__('Please complete the reCAPTCHA.'));
                return $this->render();
            }

            $http = new Client();
            $response = $http->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $recaptchaResponse,
            ]);

            $result = $response->getJson();

            if (!$result['success']) {
                $this->Flash->error(__('reCAPTCHA verification failed. Please try again.'));
                return $this->render();
            }

            // Patch the user entity with the submitted data
            $user = $this->Users->patchEntity($user, $this->request->getData());

            if ($user->getErrors()) {
                foreach ($user->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error($error);
                    }
                }
                return $this->render();
            }

            // Clear the nonce-related fields on successful password reset
            $user->nonce = null;
            $user->nonce_expiry = null;

            if ($this->Users->save($user)) {
                $this->Flash->success('Your password has been successfully reset. Please login with your new password.');
                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error('The password cannot be reset. Please try again.');
        }

        // Pass the $user variable to the view
        $this->set(compact('user'));
    }

    /**
     * Keep Alive method
     *
     * This endpoint is used to keep the user's session alive.
     * Accessing this endpoint refreshes the session cookie.
     *
     * @return \Cake\Http\Response Returns a JSON response.
     */
    public function keepAlive()
    {
        // Disable layout and view rendering for this API-like endpoint
        $this->viewBuilder()->disableAutoLayout();
        $this->autoRender = false;

        // Return a simple success response
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(['status' => 'success']));
    }
}
