<?php
/**
 * Jenbury Financial - Admin Edit User Page
 */
$this->assign('title', 'Edit User');
?>

<div class="page-header">
    <h1>Edit User</h1>
    <p>Update user information and permissions.</p>
</div>

<div class="row">
    <div class="column column-75">
        <div class="card">
            <div class="card-body">
                <?= $this->Form->create($user, ['class' => 'needs-validation', 'novalidate' => true]) ?>
                <fieldset>
                    <div class="row">
                        <div class="column">
                            <?= $this->Form->control('first_name', [
                                'class' => 'form-control',
                                'id' => 'first-name',
                                'required' => true,
                                'minlength' => 2,
                                'maxlength' => 35,
                                'pattern' => "^[A-Za-z\\s'-]+$",
                                'title' => 'Please enter a valid name (letters, spaces, hyphens, apostrophes only, 2-70 characters).'
                            ]) ?>
                            <?php // Removed validation-message div ?>
                        </div>
                        <div class="column">
                             <?= $this->Form->control('last_name', [
                                'class' => 'form-control',
                                'id' => 'last-name',
                                'required' => true,
                                'minlength' => 2,
                                'maxlength' => 60,
                                'pattern' => "^[A-Za-z\\s'-]+$",
                                'title' => 'Please enter a valid name (letters, spaces, hyphens, apostrophes only, 2-70 characters).'
                            ]) ?>
                            <?php // Removed validation-message div ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="column">
                            <?= $this->Form->control('email', [
                                'class' => 'form-control',
                                'required' => true,
                                'maxlength' => 100 // Match register form
                            ]) ?>
                            <?php // Removed validation-message div ?>
                        </div>
                        <div class="column">
                            <?= $this->Form->control('role', [
                                'options' => $roles,
                                'class' => 'form-control',
                                'label' => 'User Role',
                                'required' => true, // Added required
                                // Disable role change for the primary admin account
                                'disabled' => ($user->email === 'admin@jenburyfinancial.com')
                            ]) ?>
                            <?php // Removed validation-message div ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="column">
                            <?= $this->Form->control('is_active', [
                                'type' => 'checkbox',
                                'label' => 'Active Account'
                            ]) ?>
                        </div>
                        <div class="column">
                            <?= $this->Form->control('email_verified', [
                                'type' => 'checkbox',
                                'label' => 'Email Verified'
                            ]) ?>
                        </div>
                    </div>
                </fieldset>
                
                <div class="form-actions">
                    <?= $this->Form->button(__('Save Changes'), ['class' => 'button']) ?>
                    <?= $this->Html->link(__('Cancel'), ['action' => 'manageUsers'], ['class' => 'button button-outline']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
    
    <div class="column column-25">
        <div class="card">
            <div class="card-header">
                <h3>User Information</h3>
            </div>
            <div class="card-body">
                <ul class="user-details-list">
                    <li>
                        <strong>User ID:</strong> <?= $user->id ?>
                    </li>
                    <li>
                        <strong>Created:</strong> <?= $user->created->format('F j, Y') ?>
                    </li>
                    <li>
                        <strong>Last Modified:</strong> <?= $user->modified->format('F j, Y') ?>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Role Information</h3>
            </div>
            <div class="card-body">
                <p><strong>User Role:</strong> Regular users can access and purchase courses.</p>
                <p><strong>Admin Role:</strong> Administrators can manage courses, modules, and users.</p>
            </div>
        </div>
    </div>
</div>

<script>
     document.getElementById('first-name').addEventListener('input', function(event) {
         this.value = this.value.replace(/[^A-Za-z]/g, ''); // Replaces anything that is not a letter
     });
    
     document.getElementById('last-name').addEventListener('input', function(event) {
         this.value = this.value.replace(/[^A-Za-z]/g, ''); // Replaces anything that is not a letter
     });
</script>