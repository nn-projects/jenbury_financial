<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', 'Register');
use Cake\Core\Configure;
?>

<!-- Include Google reCAPTCHA API script -->
<?= $this->Html->script('https://www.google.com/recaptcha/api.js', ['block' => true]) ?>

<div class="row">
    <div class="column column-60 column-offset-20">
        <div class="card register-form-container">
            <div class="card-header">
                <h2>Create Your Account</h2>
                <p>Join Jenbury Financial and start your journey to financial literacy.</p>
            </div>
            <div class="card-body">
                <?= $this->Flash->render() ?>
                
                <?= @$this->Form->create($user, ['id' => 'signup-form', 'class' => 'signup-form needs-validation', 'novalidate' => true]) ?>
                    <div class="row">
                        <div class="column">
                            <div class="input">
                                <?= $this->Form->control('first_name', [
                                    'label' => 'First Name*',
                                    'required' => true,
                                    'placeholder' => 'Enter your first name',
                                    'minlength' => 2,
                                    'maxlength' => 35,
                                    'pattern' => "^[A-Za-z\\s'-]+$", // Allow letters, spaces, hyphens, apostrophes
                                    'title' => 'Please enter a valid name (letters, spaces, hyphens, apostrophes only, 2-70 characters).' // Tooltip for pattern
                                ]) ?>
                                <?php // Removed validation-message div ?>
                            </div>
                        </div>
                        <div class="column">
                            <div class="input">
                                <?= $this->Form->control('last_name', [
                                    'label' => 'Last Name*',
                                    'required' => true,
                                    'placeholder' => 'Enter your last name',
                                    'minlength' => 2,
                                    'maxlength' => 60,
                                    'pattern' => "^[A-Za-z\\s'-]+$", // Allow letters, spaces, hyphens, apostrophes
                                    'title' => 'Please enter a valid name (letters, spaces, hyphens, apostrophes only, 2-70 characters).' // Tooltip for pattern
                                ]) ?>
                                <?php // Removed validation-message div ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input">
                        <?= $this->Form->control('email', [
                            'type' => 'email',
                            'label' => 'Email Address*',
                            'required' => true,
                            'placeholder' => 'Enter your email address',
                            'maxlength' => 100
                        ]) ?>
                        <?php // Removed validation-message div ?>
                        <small>We'll never share your email with anyone else.</small>
                    </div>
                    
                    <div class="input password-input-container"> <?php // Added container class ?>
                        <?= $this->Form->control('password', [
                            'type' => 'password',
                            'label' => 'Password*',
                            'required' => true,
                            'placeholder' => 'Create a password',
                            // 'id' => 'myInput', // Removed ID
                            'maxlength' => 32,
                            'minlength' => 8, // Added minlength for validation script
                            'class' => 'password-input' // Added class
                        ]) ?>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                        <?php // Removed validation-message div ?>
                        <?php /* Removed old Show Password checkbox input */ ?>
                        <?= $this->Form->error('password', '<div class="error-message">:message</div>') // Keep server-side error ?>
                        <small>Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number and one special character.</small>
                    </div>

                    <div class="input password-input-container"> <?php // Added container class ?>
                        <?= $this->Form->control('confirm_password', [
                            'type' => 'password',
                            'label' => 'Confirm Password*',
                            'required' => true,
                            'placeholder' => 'Confirm your password',
                            'class' => 'password-input' // Added class
                        ]) ?>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                        <?php // Removed validation-message div ?>
                        <?= $this->Form->error('confirm_password', '<div class="error-message">:message</div>') // Keep server-side error ?>
                    </div>
                    
                    <div class="input checkbox">
                        <label for="terms_checkbox">
                            <?= $this->Form->checkbox('terms', ['required' => true, 'id' => 'terms_checkbox']) ?>
                            I agree to the Terms & Services
                        </label>
                        <?php // Removed validation-message div ?>
                        <?= $this->Form->error('terms', '<div class="error-message">:message</div>') // Keep server-side error ?>
                    </div>

                    <!-- <?= $this->Html->link('Terms of Service', ['controller' => 'Pages', 'action' => 'terms']) ?> and <?= $this->Html->link('Privacy Policy', ['controller' => 'Pages', 'action' => 'privacy']) ?> HYPERLINK FOR TERMS AND SERVICES -->
                    <!-- <?= $this->Html->link('Terms of Service', ['controller' => 'Pages', 'action' => 'terms']) ?> and <?= $this->Html->link('Privacy Policy', ['controller' => 'Pages', 'action' => 'privacy']) ?> HYPERLINK FOR TERMS AND SERVICES -->
                     
                    <!-- reCAPTCHA widget -->
                    <div class="recaptcha-container">
                        <div class="g-recaptcha" 
                             data-sitekey="<?= h(Configure::read('Recaptcha.siteKey')) ?>" 
                             data-callback="onSubmit"></div>
                    </div>

                    <div class="submit">
                        <?= $this->Form->button('Create Account', ['class' => 'button btn-primary']) ?>
                    </div>
                    <?php $this->Form->unlockField('g-recaptcha-response'); ?>
                <?= $this->Form->end() ?>
            </div>
            <div class="card-footer">
                <p>Already have an account? <?= $this->Html->link('Login', ['controller' => 'Users', 'action' => 'login']) ?></p>
            </div>
        </div>
        
    </div>
</div>

<?php /* Removed old inline script */ ?>
<?php // Removed include for non-existent signup.js ?>
<?php // Removed redundant name filtering script (can be added to form-validation.js if needed) ?>
