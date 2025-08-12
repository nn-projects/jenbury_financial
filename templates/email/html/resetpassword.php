<?php
/**
 * Reset Password Plain Text email template
 *
 * @var \App\View\AppView $this
 * @var string $first_name email recipient's first name
 * @var string $last_name email recipient's last name
 * @var string $email email recipient's email address
 * @var string $nonce nonce used to reset the password
 */
?>
<p>Hi <?= h($first_name) ?>,</p>

<p>We received a request to reset the password for your account at <b>Jenbury Financial Courses</b>. If you didn't make this request, please ignore this email.</p>

<p>To reset your password, click the link below:</p>

<p><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'resetPassword', $nonce], ['fullBase' => true]) ?>">Reset Account Password</a></p>

<p>Or, copy and paste the following URL into your browser:</p>

<p><?= $this->Url->build(['controller' => 'Users', 'action' => 'resetPassword', $nonce], ['fullBase' => true]) ?></p>

<p>This email is addressed to <?= h($first_name) ?> <?= h($last_name) ?> &lt;<?= h($email) ?>&gt;.<br>
Please discard this email if it is not meant for you.</p>

<style>
    p {
        font-family: Arial, sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #333;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    b {
        color: #000;
    }
</style>
