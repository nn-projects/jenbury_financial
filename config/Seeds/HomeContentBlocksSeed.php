<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * HomeContentBlocks seed.
 */
class HomeContentBlocksSeed extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'parent' => 'home',
                'slug' => 'home-page-title-tag',
                'label' => 'Homepage: Browser Title Tag',
                'description' => 'Title shown in the browser tab for the homepage.',
                'type' => 'text',
                'value' => 'Jenbury Financial Knowledge Center',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'home',
                'slug' => 'home-meta-description',
                'label' => 'Homepage: Meta Description Tag',
                'description' => 'Meta description for the homepage.',
                'type' => 'text',
                'value' => 'Welcome to Jenbury Financial Knowledge Center - Your path to financial excellence',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'home',
                'slug' => 'home-main-heading-1',
                'label' => 'Homepage: Main Heading (Part 1)',
                'description' => 'First part of the main heading on the homepage.',
                'type' => 'text',
                'value' => 'Jenbury Financial',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'home',
                'slug' => 'home-main-heading-2',
                'label' => 'Homepage: Main Heading (Part 2)',
                'description' => 'Second part of the main heading on the homepage.',
                'type' => 'text',
                'value' => 'Knowledge Center',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'home',
                'slug' => 'home-subtitle',
                'label' => 'Homepage: Subtitle',
                'description' => 'Subtitle shown below the main heading on the homepage.',
                'type' => 'text',
                'value' => 'by Andrea Jenkins',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'home',
                'slug' => 'home-cta-button-text',
                'label' => 'Homepage: Call-to-Action Button Text',
                'description' => 'Text for the call-to-action button on the homepage.',
                'type' => 'text',
                'value' => '<span>Learn More</span><span class="arrow">→</span>',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'home',
                'slug' => 'home-login-button-text',
                'label' => 'Homepage: Login Button Text',
                'description' => 'Text for the login button in the header.',
                'type' => 'text',
                'value' => 'Log In',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'home',
                'slug' => 'home-signup-button-text',
                'label' => 'Homepage: Sign Up Button Text',
                'description' => 'Text for the signup button in the header.',
                'type' => 'text',
                'value' => 'Sign Up',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
            [
                'parent' => 'home',
                'slug' => 'home-logout-button-text',
                'label' => 'Homepage: Logout Button Text',
                'description' => 'Text for the logout button in the header.',
                'type' => 'text',
                'value' => 'Logout',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
        ];

        $table = $this->table('content_blocks');
        $table->insert($data)->saveData();
    }
}