<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * NavigationContentBlocks seed.
 */
class NavigationContentBlocksSeed extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'parent' => 'Navigation',
                'slug' => 'navbar-link-courses-text',
                'label' => 'Main Nav: Courses Link',
                'description' => 'Text for the "Courses" link in the navigation menu.',
                'type' => 'text',
                'value' => 'Courses',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'Navigation',
                'slug' => 'user-dropdown-admin-dashboard-text',
                'label' => 'User Dropdown: Admin Dashboard Link',
                'description' => 'Text for the "Admin Dashboard" link in the user dropdown menu.',
                'type' => 'text',
                'value' => 'Admin Dashboard',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'Navigation',
                'slug' => 'user-dropdown-dashboard-text',
                'label' => 'User Dropdown: Dashboard Link',
                'description' => 'Text for the "Dashboard" link in the user dropdown menu.',
                'type' => 'text',
                'value' => 'Dashboard',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'Navigation',
                'slug' => 'user-dropdown-manage-site-content-text',
                'label' => 'User Dropdown: Manage Site Content Link',
                'description' => 'Text for the "Manage Site Content" link in the user dropdown menu.',
                'type' => 'text',
                'value' => 'Manage Site Content',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'Navigation',
                'slug' => 'user-dropdown-logout-text',
                'label' => 'User Dropdown: Logout Link',
                'description' => 'Text for the "Logout" link in the user dropdown menu.',
                'type' => 'text',
                'value' => 'Logout',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'Navigation',
                'slug' => 'user-nav-login-text',
                'label' => 'Logged Out Nav: Login Button',
                'description' => 'Text for the "Login" button in the navigation menu.',
                'type' => 'text',
                'value' => 'Login',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'Navigation',
                'slug' => 'user-nav-register-text',
                'label' => 'Logged Out Nav: Register Button',
                'description' => 'Text for the "Register" button in the navigation menu.',
                'type' => 'text',
                'value' => 'Register',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
        ];

        $table = $this->table('content_blocks');
        $table->insert($data)->saveData();
    }
}