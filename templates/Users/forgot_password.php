<?php
/**
 * Jenbury Financial - Login Page
 */
$this->assign('title', 'Forgot Password');
?>

<div class="row">
    <div class="column column-50 column-offset-25">
        <div class="card login-form-container">
            <div class="card-header">
                <h2 class="login-title">Forgot Password</h2>
                <p class="login-subtitle">Enter your email to reset your password</p>
            </div>
            <div class="card-body">
                <?= $this->Form->create(null, ['class' => 'login-form needs-validation', 'url' => ['controller' => 'Users', 'action' => 'forgotPassword'], 'novalidate' => true]) ?>
                        <div class="input"> <?php // Changed class from form-group to input ?>
                            <label for="email" class="form-label">Email</label>
                            <?= $this->Form->control('email', [
                                'required' => true,
                                'label' => false,
                                'class' => 'form-input', // Keep existing class if needed, JS targets input element directly
                                'type' => 'email',
                                'autofocus' => true,
                                'maxlength' => 65,
                                'placeholder' => 'your.email@example.com'
                            ]) ?>
                            <?php // Removed validation-message div ?>
                        </div>
                
                        <?= $this->Form->button(__('Reset Password'), ['class' => 'btn btn-primary']) ?>
                    <?= $this->Form->end() ?>
            </div>
            <div class="card-footer">
                <p>Remember your password? <?= $this->Html->link('Back to login',['controller' => 'Users', 'action' => 'login'],['class' => 'signup-link'])?></p>
            </div>
        </div>
    </div>
</div>
    







<!--
        <div class="login-container">
            <div class="login-card">
                <div class="login-card-content">
                    <div class="login-header">
                        <h1 class="login-title">Forgot Password</h1>
                        <p class="login-subtitle">Enter your email to reset your password</p>
                    </div>
            
                    <?php if ($this->Flash->render()): ?>
                        <?= $this->Flash->render() ?>
                    <?php endif; ?>
                    <?php // Removed redundant Form->create() ?>
            
                    <?= $this->Form->create(null, ['class' => 'login-form needs-validation', 'url' => ['controller' => 'Users', 'action' => 'forgotPassword'], 'novalidate' => true]) ?>
                        <div class="input"> <?php // Changed class from form-group to input ?>
                            <label for="email" class="form-label">Email</label>
                            <?= $this->Form->control('email', [
                                'required' => true,
                                'label' => false,
                                'class' => 'form-input', // Keep existing class if needed, JS targets input element directly
                                'type' => 'email',
                                'autofocus' => true,
                                'maxlength' => 55,
                                'placeholder' => 'your.email@example.com'
                            ]) ?>
                            <?php // Removed validation-message div ?>
                        </div>
                
                        <?= $this->Form->button(__('Reset Password'), ['class' => 'btn btn-primary']) ?>
                    <?= $this->Form->end() ?>
             
                    <div class="signup-prompt">
                        Remember your password?
                        <?= $this->Html->link('Back to login',
                            ['controller' => 'Users', 'action' => 'login'],
                            ['class' => 'signup-link'])
                        ?>
                    </div>
                </div>
        -->
        <!--
        <div class="login-image-container">
            <img 
                src="https://placehold.co/600x800/20809a/FFFFFF/png?text=Jenbury+Financial" 
                srcset="
                    https://placehold.co/600x800/20809a/FFFFFF/png?text=Jenbury+Financial 600w,
                    https://placehold.co/1200x1600/20809a/FFFFFF/png?text=Jenbury+Financial 1200w
                " 
                sizes="(min-width: 768px) 50vw, 100vw" 
                alt="Jenbury Financial" 
                class="login-image">
        </div>
        
    </div>
</div>
-->
