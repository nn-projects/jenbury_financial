<?php
/**
 * Jenbury Financial - User Profile Page
 */
$this->assign('title', 'My Profile');
?>

<div class="page-header">
    <h1>My Profile</h1>
</div>

<div class="row">
    <div class="column column-25">
        <div class="card">
            <div class="card-body text-center">
                <div class="profile-image">
                    <img src="https://via.placeholder.com/150" alt="<?= h($user->first_name . ' ' . $user->last_name) ?>" class="img-circle">
                </div>
                <h3><?= h($user->first_name . ' ' . $user->last_name) ?></h3>
                <p class="text-muted">Member since <?= $user->created->format('F Y') ?></p>
                
                <div class="profile-actions">
                    <?= $this->Html->link('Change Password', ['controller' => 'Users', 'action' => 'changePassword'], ['class' => 'button button-outline']) ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="column column-50">
        <div class="card">
            <div class="card-header">
                <h2>Personal Information</h2>
            </div>
            <div class="card-body profile-form-body">
                <?= $this->Flash->render() ?>
                
                <?= $this->Form->create($user, ['class' => 'needs-validation', 'novalidate' => true]) ?>
                    <div class="row">
                        <div class="column">
                            <div class="input">
                                <?= $this->Form->control('first_name', [
                                    'label' => 'First Name',
                                    'required' => true,
                                    'class' => 'profile-input', // Keep existing class
                                    'minlength' => 2,
                                    'maxlength' => 35,
                                    'pattern' => "^[A-Za-z\\s']+$", // Allow letters, hyphens, apostrophes
                                    'title' => 'Please enter a valid name (letters, spaces, hyphens, apostrophes only, 2-70 characters).'
                                ]) ?>
                                <?php // Removed validation-message div ?>
                            </div>
                        </div>
                        <div class="column">
                            <div class="input">
                                <?= $this->Form->control('last_name', [
                                    'label' => 'Last Name',
                                    'required' => true,
                                    'class' => 'profile-input', // Keep existing class
                                    'minlength' => 2,
                                    'maxlength' => 60,
                                    'pattern' => "^[A-Za-z\\s']+$", // Allow letters, spaces, apostrophes
                                    'title' => 'Please enter a valid name (letters, spaces, hyphens, apostrophes only, 2-70 characters).'
                                ]) ?>
                                <?php // Removed validation-message div ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input">
                        <?= $this->Form->control('email', [
                            'type' => 'email',
                            'label' => 'Email Address',
                            'required' => true,
                            'class' => 'profile-input', // Keep existing class
                            'maxlength' => 100 // Match register form
                        ]) ?>
                        <?php // Removed validation-message div ?>
                    </div>
                    
                    <div class="submit">
                        <?= $this->Form->button('Update Profile', ['class' => 'button button-primary']) ?>
                    </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
    
    <div class="column column-20">
        <div class="card">
            <div class="card-header">
                <h3>Quick Links</h3>
            </div>
            <div class="card-body">
                <ul class="nav-list">
                    <li><?= $this->Html->link('Dashboard', ['controller' => 'Dashboard', 'action' => 'index']) ?></li>
                    <li><?= $this->Html->link('My Courses', ['controller' => 'Dashboard', 'action' => 'myCourses']) ?></li>
                    <li><?= $this->Html->link('My Modules', ['controller' => 'Dashboard', 'action' => 'myModules']) ?></li>
                    <li><?= $this->Html->link('Purchase History', ['controller' => 'Dashboard', 'action' => 'purchaseHistory']) ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>


        
        <!-- <div class="card">
            <div class="card-header">
                <h2>Account Settings</h2>
            </div>
            <div class="card-body">
                <div class="setting-item">
                    <div class="row">
                        <div class="column column-75">
                            <h3>Email Notifications</h3>
                            <p>Receive email notifications about new courses, updates, and special offers.</p>
                        </div>
                        <div class="column column-25 text-right">
                            <div class="toggle-switch">
                                <input type="checkbox" id="email-notifications" class="toggle-input" checked>
                                <label for="email-notifications" class="toggle-label"></label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="row">
                        <div class="column column-75">
                            <h3>Two-Factor Authentication</h3>
                            <p>Add an extra layer of security to your account.</p>
                        </div>
                        <div class="column column-25 text-right">
                            <div class="toggle-switch">
                                <input type="checkbox" id="two-factor" class="toggle-input">
                                <label for="two-factor" class="toggle-label"></label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="row">
                        <div class="column column-75">
                            <h3>Account Privacy</h3>
                            <p>Control who can see your profile and activity.</p>
                        </div>
                        <div class="column column-25 text-right">
                            <select class="form-control">
                                <option value="public">Public</option>
                                <option value="private" selected>Private</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div> --> 
        
        <!-- <div class="card">
            <div class="card-header">
                <h2>Danger Zone</h2>
            </div>
            <div class="card-body">
                <div class="danger-item">
                    <div class="row">
                        <div class="column column-75">
                            <h3>Delete Account</h3>
                            <p>Permanently delete your account and all associated data. This action cannot be undone.</p>
                        </div>
                        <div class="column column-25 text-right">
                            <button class="button button-outline button-danger">Delete Account</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->

<script>
     document.getElementById('first-name').addEventListener('input', function(event) {
         this.value = this.value.replace(/[^A-Za-z]/g, ''); // Replaces anything that is not a letter
     });
    
     document.getElementById('last-name').addEventListener('input', function(event) {
         this.value = this.value.replace(/[^A-Za-z]/g, ''); // Replaces anything that is not a letter
     });
</script>