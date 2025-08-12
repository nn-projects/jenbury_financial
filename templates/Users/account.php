<?php
/**
 * Jenbury Financial - My Account Page (Consolidated)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user The logged-in user entity
 * @var \Cake\Datasource\ResultSetInterface $purchases User's purchase history
 */
$this->assign('title', 'My Account');
?>

<div class="page-header">
    <h1>My Account</h1>
</div>

<?= $this->Flash->render() ?>

<div class="account-page row">

    <div class="column column-75"> <?php // Main content column ?>

        <?php // Internal Page Navigation (Tabs) ?>
        <nav class="account-nav-tabs">
            <ul>
                <li><a href="#profile">Profile Information</a></li>
                <li><a href="#security">Change Password</a></li>
                <li><a href="#history">Purchase History</a></li>
            </ul>
        </nav>
        <hr>

        <?php // === Profile Information Section === ?>
        <section id="profile" class="account-section card">
            <div class="card-header">
                <h2>Profile Information</h2>
            </div>
            <div class="card-body">
                <p>Member since: <?= $user->created->format('F Y') ?></p>
                <p>User Role: <?= ucfirst($user->role) ?></p>

                <?= $this->Form->create($user, ['url' => ['action' => 'account'], 'class' => 'needs-validation', 'novalidate' => true]) ?>
                    <fieldset>
                        <legend><?= __('Edit Your Profile') ?></legend>
                        <div class="row">
                            <div class="column">
                                <?= $this->Form->control('first_name', [
                                    'label' => 'First Name*',
                                    'required' => true,
                                    'minlength' => 2,
                                    'maxlength' => 35,
                                    'pattern' => "^[A-Za-z\\s']+$",
                                    'title' => 'Please enter a valid name (letters, spaces, apostrophes only, 2-35 characters).'
                                ]) ?>
                            </div>
                            <div class="column">
                                <?= $this->Form->control('last_name', [
                                    'label' => 'Last Name*',
                                    'required' => true,
                                    'minlength' => 2,
                                    'maxlength' => 20,
                                    'pattern' => "^[A-Za-z\\s'-]+$",
                                    'title' => 'Please enter a valid name (letters, spaces, hyphens, apostrophes only, 2-20 characters).'
                                ]) ?>
                            </div>
                        </div>
                        <?= $this->Form->control('email', [
                            'type' => 'email',
                            'label' => 'Email Address*',
                            'required' => true,
                            'maxlength' => 100
                        ]) ?>
                    </fieldset>
                    <?= $this->Form->button(__('Update Profile'), ['class' => 'button btn-primary']) ?>
                <?= $this->Form->end() ?>
            </div>
        </section>

        <?php // === Security Section === ?>
        <section id="security" class="account-section card">
            <div class="card-header">
                <h2>Change Password</h2>
            </div>
            <div class="card-body">
                <?= $this->Form->create(null, ['url' => ['action' => 'account'], 'id' => 'change-password-form', 'class' => 'needs-validation', 'novalidate' => true]) ?>
                    <fieldset>
                        <legend><?= __('Update Your Password') ?></legend>
                        <?php // Assuming password toggle JS/CSS is loaded globally via layout ?>
                        <div class="input password-input-container">
                            <?= $this->Form->control('current_password', [
                                'type' => 'password',
                                'label' => 'Current Password*',
                                'required' => true,
                                'placeholder' => 'Enter your current password',
                                'class' => 'password-input'
                            ]) ?>
                            <button type="button" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>

                        <div class="input password-input-container">
                            <?= $this->Form->control('new_password', [
                                'type' => 'password',
                                'label' => 'New Password*',
                                'required' => true,
                                'placeholder' => 'Enter your new password',
                                'minlength' => 8,
                                'maxlength' => 32,
                                'class' => 'password-input',
                                'id' => 'new-password' // Added ID for potential strength meter JS
                            ]) ?>
                             <button type="button" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                            <small>Password must be at least 8 characters long and meet complexity requirements (if any).</small>
                            <?php // Add password strength meter display here if implemented ?>
                        </div>

                        <div class="input password-input-container">
                            <?= $this->Form->control('confirm_password', [
                                'type' => 'password',
                                'label' => 'Confirm New Password*',
                                'required' => true,
                                'placeholder' => 'Confirm your new password',
                                'maxlength' => 32,
                                'class' => 'password-input'
                            ]) ?>
                             <button type="button" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </fieldset>
                    <?= $this->Form->button(__('Update Password'), ['class' => 'button btn-primary']) ?>
                <?= $this->Form->end() ?>

                <div class="password-tips">
                    <h3>Password Security Tips</h3>
                    <ul>
                        <li>Use a unique password for this site.</li>
                        <li>Use a mix of letters, numbers, and symbols.</li>
                        <li>Avoid using easily guessable information.</li>
                        <li>Consider using a password manager.</li>
                    </ul>
                </div>
            </div>
        </section>

        <?php // === Purchase History Section === ?>
        <section id="history" class="account-section card">
            <div class="card-header">
                <h2>Purchase History</h2>
            </div>
            <div class="card-body">
                <?php if (!$purchases->items()->isEmpty()): // Use items()->isEmpty() ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th><?= $this->Paginator->sort('Purchases.created', 'Date') ?></th>
                                    <th>Item</th>
                                    <th>Type</th>
                                    <th><?= $this->Paginator->sort('Purchases.amount', 'Amount') ?></th>
                                    <th><?= $this->Paginator->sort('Purchases.payment_status', 'Payment Status') ?></th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchases as $purchase): ?>
                                    <tr>
                                        <td><?= $purchase->created->format('M d, Y') ?></td>
                                        <td>
                                            <?php if ($purchase->course): ?>
                                                <?= h($purchase->course->title) ?>
                                            <?php elseif ($purchase->module): ?>
                                                <?= h($purchase->module->title) ?>
                                            <?php else: ?>
                                                Unknown Item
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($purchase->course): ?>
                                                Course
                                            <?php elseif ($purchase->module): ?>
                                                Module
                                            <?php else: ?>
                                                Unknown
                                            <?php endif; ?>
                                        </td>
                                        <td>$<?= number_format($purchase->actual_amount_paid, 2) ?></td>
                                        <td>
                                            <?php // Status Badges - adapt classes as needed ?>
                                            <?php if ($purchase->payment_status === 'completed'): ?>
                                                <span class="badge badge-success">Completed</span>
                                            <?php elseif ($purchase->payment_status === 'pending'): ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php elseif ($purchase->payment_status === 'failed'): ?>
                                                <span class="badge badge-danger">Failed</span>
                                            <?php elseif ($purchase->payment_status === 'refunded'): ?>
                                                <span class="badge badge-info">Refunded</span>
                                            <?php else: ?>
                                                 <?= h(ucfirst($purchase->payment_status)) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($purchase->payment_status === 'completed'): ?>
                                                <?php if ($purchase->course): ?>
                                                    <?= $this->Html->link('View Course', ['controller' => 'Courses', 'action' => 'view', $purchase->course->id], ['class' => 'button button-small btn-primary']) ?>
                                                <?php elseif ($purchase->module): ?>
                                                    <?= $this->Html->link('View Module', ['controller' => 'Modules', 'action' => 'view', $purchase->module->id], ['class' => 'button button-small btn-primary']) ?>
                                                <?php endif; ?>
                                            <?php elseif ($purchase->payment_status === 'pending'): ?>
                                                <?php // Link to payment completion if applicable ?>
                                                <?= $this->Html->link('Complete Payment', '#', ['class' => 'button button-small button-outline', 'disabled' => true]) ?>
                                            <?php elseif ($purchase->payment_status === 'failed'): ?>
                                                 <?php // Link to retry payment if applicable ?>
                                                <?= $this->Html->link('Retry Payment', '#', ['class' => 'button button-small button-outline', 'disabled' => true]) ?>
                                            <?php endif; ?>
                                            <?php // Add link to view order details if such a page exists ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-container">
                        <ul class="pagination">
                            <?= $this->Paginator->prev('« Previous') ?>
                            <?= $this->Paginator->numbers() ?>
                            <?= $this->Paginator->next('Next »') ?>
                        </ul>
                        <p><?= $this->Paginator->counter('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total') ?></p>
                    </div>
                <?php else: ?>
                    <div class="no-purchases">
                        <p>You haven't made any purchases yet.</p>
                        <?= $this->Html->link('Browse Courses', ['controller' => 'Courses', 'action' => 'index'], ['class' => 'button']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </div> <?php // End main content column ?>

    <div class="column column-25"> <?php // Sidebar column (optional) ?>
        <div class="card">
             <div class="card-header">
                 <h3>Account Links</h3>
             </div>
             <div class="card-body">
                 <ul class="nav-list">
                     <li><?= $this->Html->link('Dashboard', ['controller' => 'Dashboard', 'action' => 'index']) ?></li>
                     <li><?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout']) ?></li>
                 </ul>
             </div>
        </div>
         <div class="card">
             <div class="card-header">
                 <h3>Need Help?</h3>
             </div>
             <div class="card-body">
                 <p>If you have questions about your account or purchases, please contact us.</p>
                 <?= $this->Html->link('Contact Support', 'https://www.jenbury.com.au/contact', ['class' => 'button btn-primary', 'target' => '_blank']) ?>
             </div>
        </div>
    </div> <?php // End sidebar column ?>

</div> <?php // End .account-page .row ?>

<?php // Add any necessary page-specific CSS or JS here or enqueue them ?>
<style>
/* --- General Layout & Separation --- */
.account-section {
    margin-bottom: 3rem; /* Increased margin for better separation */
    /* padding-bottom: 1rem;  Removed this, will use padding on card-body */
}
.account-page .card-body { /* Apply padding to ALL card bodies on the page */
    padding: 20px; /* Add padding inside the card body */
}
hr { margin-bottom: 2rem; } /* Add space after the hr */

/* --- Tab Navigation Styling --- */
.account-nav-tabs {
    border-bottom: 1px solid #ccc; /* Line below tabs */
    margin-bottom: 2rem; /* Space below tabs */
}
.account-nav-tabs ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.account-nav-tabs li {
    display: inline-block; /* Arrange tabs horizontally */
    margin-bottom: -1px; /* Overlap bottom border */
}
.account-nav-tabs a {
    display: block;
    padding: 10px 15px;
    border: 1px solid #6a0dad; /* Purple border */
    border-bottom: none; /* Remove bottom border for the tab itself */
    background-color: #e6e0f5; /* Light purple background */
    text-decoration: none;
    color: #333; /* Dark text for light background */
    border-radius: 4px 4px 0 0; /* Rounded top corners */
    margin-right: 5px; /* Space between tabs */
}
.account-nav-tabs a:hover {
    background-color: #d1c4e9; /* Slightly darker purple on hover */
    color: #000;
}
/* Add active state styling later if needed with JS */
/* Example active state:
.account-nav-tabs li.active a {
    background-color: #fff;
    border-color: #6a0dad;
    border-bottom-color: #fff; // Hide bottom border part that overlaps
    color: #6a0dad;
}
*/

/* --- Form Styling --- */
.account-section form .input {
    margin-bottom: 1.5rem; /* Increased spacing between form inputs */
}
.account-section form input[type="text"],
.account-section form input[type="email"],
.account-section form input[type="password"] {
    width: 100%; /* Make inputs take full width of their container */
    box-sizing: border-box; /* Include padding and border in the element's total width and height */
}
.account-section form .button {
    margin-top: 1rem; /* Add space above buttons */
}
.password-tips { margin-top: 1.5rem; font-size: 0.9em; color: #555; }
.password-tips ul { list-style: disc; padding-left: 20px; }
.password-input-container { position: relative; }
.password-toggle-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-70%); /* Fine-tune vertical alignment */
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    color: #777;
}
.password-toggle-btn i { font-size: 1.1em; }

/* --- Purchase History Table Styling --- */
#history table {
    width: 100%;
    border-collapse: collapse; /* Remove double borders */
    margin-top: 1rem;
}
#history th,
#history td {
    border: 1px solid #ddd; /* Add borders */
    padding: 10px;         /* Add padding */
    text-align: left;
    vertical-align: middle;
}
#history th {
    background-color: #f2f2f2; /* Light grey header */
    font-weight: bold;
    color: #333; /* Ensure consistent header text color */
}
/* Ensure links within headers have the same color */
#history th a {
    color: inherit; /* Inherit color from th */
    text-decoration: none; /* Optional: remove underline */
}
#history th a:hover {
    text-decoration: underline; /* Optional: add underline on hover */
}
#history tbody tr:nth-child(odd) {
    background-color: #f9f9f9; /* Zebra striping */
}
#history tbody tr:hover {
    background-color: #f1f1f1; /* Hover effect */
}
.pagination-container { margin-top: 1.5rem; }

/* --- Badge Styling (Keep existing) --- */
.badge {
    display: inline-block;
    padding: .25em .6em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25rem;
    color: #fff;
}
.badge-success { background-color: #28a745; }
.badge-warning { background-color: #ffc107; color: #212529;}
.badge-danger { background-color: #dc3545; }
.badge-info { background-color: #17a2b8; }
</style>

<?php $this->append('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollFlag = 'scrollToBottomAfterSort_AccountHistory'; // Unique flag name

    // Check if we need to scroll down after a sort click
    if (sessionStorage.getItem(scrollFlag) === 'true') {
        const historySection = document.getElementById('history');
        if (historySection) {
            // Scroll to the top of the history section
            historySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        sessionStorage.removeItem(scrollFlag); // Clear the flag
    }
// Add click listeners to the sort links in the purchase history table
const sortLinks = document.querySelectorAll('#history thead th a');
sortLinks.forEach(link => {
    link.addEventListener('click', function(event) {
        // Set the flag before the page navigation occurs
        sessionStorage.setItem(scrollFlag, 'true');
        // No need to preventDefault, allow the link to navigate
    });
});
// Re-enabled the sort link listener

    // --- Input Sanitization for Name Fields ---
    const firstNameInput = document.getElementById('first-name');
    const lastNameInput = document.getElementById('last-name');

    if (firstNameInput) {
        firstNameInput.addEventListener('input', function(event) {
            // Allow letters, spaces, apostrophes (matches existing pattern)
            const allowedChars = /[^A-Za-z\s']/g;
            const sanitizedValue = this.value.replace(allowedChars, '');
            if (this.value !== sanitizedValue) {
                this.value = sanitizedValue;
                // Optionally trigger a change event if needed by other scripts
                // this.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }

    if (lastNameInput) {
        lastNameInput.addEventListener('input', function(event) {
            // Allow letters, spaces, apostrophes, hyphens (matches existing pattern)
            const allowedChars = /[^A-Za-z\s'-]/g;
             const sanitizedValue = this.value.replace(allowedChars, '');
            if (this.value !== sanitizedValue) {
                this.value = sanitizedValue;
                // Optionally trigger a change event if needed by other scripts
                // this.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }
    // --- End Input Sanitization ---

});
</script>
<?php $this->end(); ?>