<?php
declare(strict_types=1);

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Migrations\AbstractSeed;

class AdminUserSeed extends AbstractSeed
{
    public function run(): void
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        
        // Admin user details
        $email = 'admin@jenburyfinancial.com';
        $password = 'Admin123!@#';
        $firstName = 'Admin';
        $lastName = 'User';
        
        // Check if admin exists
        $adminUser = $usersTable->find()
            ->where(['email' => $email])
            ->first();
            
        if (!$adminUser) {
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
                return;
            }
        } else {
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
                return;
            }
        }
        
        echo "\nAdmin account details:\n";
        echo "Email: $email\n";
        echo "Password: $password\n";
        echo "Please change this password after first login!\n";
    }
}