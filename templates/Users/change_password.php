<?php
/**
 * Jenbury Financial - Change Password Page
 */
$this->assign('title', 'Change Password');
?>

<div class="row">
    <div class="column column-50 column-offset-25">
        <div class="card change-password-form-container">
            <div class="card-header">
                <h2>Change Your Password</h2>
            </div>
            <div class="card-body">
                <?= $this->Flash->render() ?>
                
                <?= $this->Form->create($user, ['id' => 'change-password-form', 'class' => 'needs-validation', 'novalidate' => true]) ?>
                    <div class="input password-input-container"> <?php // Added container class ?>
                        <?= $this->Form->control('current_password', [
                            'type' => 'password',
                            'label' => 'Current Password*',
                            'required' => true,
                            'placeholder' => 'Enter your current password',
                            'class' => 'password-input' // Added class
                        ]) ?>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                        <?php // Removed validation-message div ?>
                    </div>
                    
                    <div class="input password-input-container"> <?php // Added container class ?>
                        <?= $this->Form->control('new_password', [
                            'type' => 'password',
                            'label' => 'New Password*',
                            'required' => true,
                            'placeholder' => 'Enter your new password',
                            'minlength' => 8, // Added for JS validation based on help text
                            'maxlength' => 32, // Added maxlength
                            'class' => 'password-input' // Added class
                        ]) ?>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                        <?php // Removed validation-message div ?>
                        <small>Password must be at least 8 characters long and include at least one uppercase letter and one special character (e.g., !@#$%^&*).</small>
                    </div>
                    
                    <div class="input password-input-container"> <?php // Added container class ?>
                        <?= $this->Form->control('confirm_password', [
                            'type' => 'password',
                            'label' => 'Confirm New Password*',
                            'required' => true,
                            'placeholder' => 'Confirm your new password',
                            'maxlength' => 32, // Added maxlength
                            'class' => 'password-input' // Added class
                        ]) ?>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                        <?php // Removed validation-message div ?>
                    </div>
                    
                    <div class="submit">
                        <?= $this->Form->button('Update Password', ['class' => 'button btn-primary']) ?>
                    </div>
                <?= $this->Form->end() ?>
            </div>
            <div class="card-footer">
                <?= $this->Html->link('Back to Profile', ['controller' => 'Users', 'action' => 'profile'], ['class' => 'button button-outline']) ?>
            </div>
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

<?php // Removed redundant password match script (handled by form-validation.js) ?>