<?php
/**
 * Jenbury Financial - Login Page
 */
$this->assign('title', 'Login');
?>

<div class="row">
    <div class="column column-50 column-offset-25">
        <div class="card login-form-container">
            <div class="card-header">
                <h2>Login to Your Account</h2>
            </div>
            <div class="card-body">
                <?= $this->Flash->render() ?>
                
                <?= $this->Form->create(null, ['class' => 'needs-validation', 'novalidate' => true]) ?>
                    <div class="input">
                        <?= $this->Form->control('email', [
                            'type' => 'email',
                            'label' => 'Email Address',
                            'required' => true,
                            'placeholder' => 'Enter your email address'
                        ]) ?>
                        <?php // Removed validation-message div ?>
                    </div>
                    
                    <div class="input password-input-container"> <?php // Added container class ?>
                        <?= $this->Form->control('password', [
                            'type' => 'password',
                            'label' => 'Password',
                            'required' => true,
                            'placeholder' => 'Enter your password',
                            // 'id' => 'myInput', // Removed ID
                            'maxlength' => 32,
                            'class' => 'password-input' // Added class for targeting
                        ]) ?>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                        <?php // Removed validation-message div ?>
                    </div>

                    <?php /* Removed old Show Password checkbox input */ ?>
                    
                    <div class="row">
                        <div class="column">
                            <div class="input checkbox">
                                <?= $this->Form->checkbox('remember_me') ?>
                                <label for="remember-me">Remember me</label>
                            </div>
                        </div>
                        <div class="column text-right">
                            <?= $this->Html->link('Forgot Password?', ['controller' => 'Users', 'action' => 'forgotPassword']) ?>
                        </div>
                    </div>
                    
                    <div class="submit">
                        <?= $this->Form->button('Login', ['class' => 'button btn-primary']) ?>
                    </div>
                <?= $this->Form->end() ?>
            </div>
            <div class="card-footer">
                <p>Don't have an account? <?= $this->Html->link('Register', ['controller' => 'Users', 'action' => 'register']) ?></p>
            </div>
        </div>
        
        <div class="login-help">
            <h3>Need Help?</h3>
            <p>If you're having trouble logging in, please contact our support team at <a href="mailto:support@jenburyfinancial.com">support@jenburyfinancial.com</a> or call us at <a href="tel:+61399999999">+61 3 9999 9999</a>.</p>
        </div>
    </div>
</div>

<?php /* Removed old inline script */ ?>
