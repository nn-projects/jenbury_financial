<?php
// Script to create admin user if it doesn't exist

// Initialize CakePHP application
require 'webroot' . DIRECTORY_SEPARATOR . 'index.php';

use Cake\ORM\TableRegistry;
use Authentication\PasswordHasher\DefaultPasswordHasher;

// Admin user details
$email = 'admin@jenburyfinancial.com';
$password = 'Admin123!@#'; // Strong default password
$firstName = 'Admin';
$lastName = 'User';

// Get the users table
$usersTable = TableRegistry::getTableLocator()->get('Users');

// Check if admin user exists
$adminUser = $usersTable->find()
    ->where(['email' => $email])
    ->first();

if (!$adminUser) {
    // Create new admin user
    $adminUser = $usersTable->newEntity([
        'email' => $email,
        'password' => $password,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'is_active' => true,
        'email_verified' => true,
        'role' => 'admin'
    ]);
    
    if ($usersTable->save($adminUser)) {
        echo "Admin user created successfully!\n";
    } else {
        echo "Error creating admin user:\n";
        print_r($adminUser->getErrors());
        exit(1);
    }
} else {
    // Update existing admin user
    $adminUser = $usersTable->patchEntity($adminUser, [
        'password' => $password,
        'role' => 'admin',
        'is_active' => true,
        'email_verified' => true
    ]);
    
    if ($usersTable->save($adminUser)) {
        echo "Existing admin user updated successfully!\n";
    } else {
        echo "Error updating admin user:\n";
        print_r($adminUser->getErrors());
        exit(1);
    }
}

echo "\nAdmin account details:\n";
echo "Email: $email\n";
echo "Password: $password\n";
echo "Please change this password after first login!\n";