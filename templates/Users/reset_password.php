<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<?php 
use Cake\Core\Configure;
$this->assign('title', 'Reset Password'); // Assign title here
?>

<!-- Include Google reCAPTCHA API script -->
<?= $this->Html->script('https://www.google.com/recaptcha/api.js', ['block' => true]) ?>

<div class="reset-password-page"> <?php // Added wrapper class ?>
    <div class="login-container">
        <div class="login-card">
            <div class="login-card-content">
                <div class="login-header">
                    <h1 class="login-title">Reset Password</h1>
                    <p class="login-subtitle">Create a new password for your account</p>
                </div>
                
                <?= $this->Flash->render() ?>
                
                <?= @$this->Form->create($user, ['id' => 'reset-password-form', 'class' => 'login-form needs-validation', 'novalidate' => true]) ?>
                    <div class="input password-input-container"> <?php // Changed class, Added container class ?>
                        <label for="password" class="form-label">New Password</label>
                        <?= $this->Form->control('password', [
                            'required' => true,
                            'label' => false,
                            'class' => 'form-input password-input', // Added password-input class
                            'type' => 'password',
                            // 'id' => 'password', // Removed ID
                            'autofocus' => true,
                            'value' => '',
                            'minlength' => 8, // Assuming standard password requirements
                            'maxlength' => 32 // Added maxlength
                        ]) ?>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                        <?php // Removed validation-message div ?>
                        <?= $this->Form->error('password', '<div class="error-message">:message</div>') // Keep server-side error ?>
                    </div>

                    <div class="input password-input-container"> <?php // Changed class, Added container class ?>
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <?= $this->Form->control('confirm_password', [
                            'required' => true,
                            'label' => false,
                            'class' => 'form-input password-input', // Added password-input class
                            'type' => 'password',
                            // 'id' => 'confirm_password', // Removed ID
                            'value' => '',
                            'maxlength' => 32 // Added maxlength
                        ]) ?>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                        <?php // Removed validation-message div ?>
                        <?= $this->Form->error('confirm_password', '<div class="error-message">:message</div>') // Keep server-side error ?>
                    </div>

                    <!-- reCAPTCHA widget -->
                    <div class="recaptcha-container">
                        <div class="g-recaptcha" 
                             data-sitekey="<?= h(Configure::read('Recaptcha.siteKey')) ?>" 
                             data-callback="onSubmit"></div>
                    </div>

                    <div class="submit">
                        <?= $this->Form->button(__('Reset Password'), ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php $this->Form->unlockField('g-recaptcha-response'); ?>
                <?= $this->Form->end() ?>
                
                <div class="signup-prompt">
                    Remember your password?
                    <?= $this->Html->link('Back to login',
                        ['controller' => 'Users', 'action' => 'login'],
                        ['class' => 'signup-link'])
                    ?>
                </div>
            </div>
            
            <div class="login-image-container">
                <img src="https://placehold.co/600x800/20809a/FFFFFF/png?text=Jenbury+Financial" alt="Jenbury Financial" class="login-image">
            </div>
            
            <div class="password-tips">
                <h3>Password Security Tips</h3>
                <ul>
                    <li>Use a unique password for each of your important accounts</li>
                    <li>Use a mix of letters, numbers, and symbols</li>
                    <li>Avoid using easily guessable information like birthdays or names</li>
                    <li>Consider using a password manager to generate and store strong passwords</li>
                    <li>Change your passwords periodically, especially for sensitive accounts</li>
                </ul>
            </div>
        </div>
    </div>
</div> <?php // End reset-password-page wrapper ?>

<?php // Removed redundant password match script (handled by form-validation.js) ?>

<?php // Removed embedded styles - moved to _reset-password.css ?>
