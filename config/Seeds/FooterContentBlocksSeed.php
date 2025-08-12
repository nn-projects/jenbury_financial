<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * FooterContentBlocks seed.
 */
class FooterContentBlocksSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            // Copyright and Legal Text
            [
                'parent' => 'Footer',
                'slug' => 'footer-copyright-prefix',
                'label' => 'Footer: Copyright Prefix',
                'description' => 'Text before the copyright year in the footer.',
                'type' => 'text',
                'value' => '© Copyright 2015-',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-copyright-suffix',
                'label' => 'Footer: Copyright Suffix',
                'description' => 'Text after the copyright year in the footer.',
                'type' => 'text',
                'value' => ' Jenbury Financial ABN ',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-abn',
                'label' => 'Footer: ABN Number',
                'description' => 'The ABN number displayed in the footer.',
                'type' => 'text',
                'value' => '15 089 512 587',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-corp-rep-prefix',
                'label' => 'Footer: Corp Rep Prefix',
                'description' => 'Text before the Corporate Representative Number in the footer.',
                'type' => 'text',
                'value' => ' is a Corporate Authorised Representative (No. ',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-corp-rep-no',
                'label' => 'Footer: Corporate Representative Number',
                'description' => 'The Corporate Representative Number displayed in the footer.',
                'type' => 'text',
                'value' => '1285213',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-corp-rep-suffix',
                'label' => 'Footer: Corp Rep Suffix',
                'description' => 'Text after the Corporate Representative Number in the footer.',
                'type' => 'text',
                'value' => ') of Alliance Wealth Pty Ltd AFSL ',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-afsl',
                'label' => 'Footer: AFSL Number',
                'description' => 'The AFSL Number displayed in the footer.',
                'type' => 'text',
                'value' => '449221',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-afsl-suffix',
                'label' => 'Footer: AFSL Suffix',
                'description' => 'Text after the AFSL Number in the footer.',
                'type' => 'text',
                'value' => ' ABN 93 161 647 007',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            // Contact Details
            [
                'parent' => 'Footer',
                'slug' => 'footer-phone-prefix',
                'label' => 'Footer: Phone Prefix',
                'description' => 'Text before the phone number in the footer.',
                'type' => 'text',
                'value' => 'Phone: ',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-phone-number',
                'label' => 'Footer: Phone Number',
                'description' => 'The phone number displayed in the footer.',
                'type' => 'text',
                'value' => '(03) 9762 0640',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-email-prefix',
                'label' => 'Footer: Email Prefix',
                'description' => 'Text before the email address in the footer.',
                'type' => 'text',
                'value' => 'Email: ',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-email-address',
                'label' => 'Footer: Email Address',
                'description' => 'The email address displayed in the footer.',
                'type' => 'text',
                'value' => 'admin@jenbury.com.au',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            // Link URLs
            [
                'parent' => 'Footer',
                'slug' => 'footer-link-fsg-url',
                'label' => 'Footer Link: Financial Services Guide',
                'description' => 'URL for the Financial Services Guide link in the footer.',
                'type' => 'text',
                'value' => 'https://www.centrepointalliance.com.au/wp-content/uploads/2024/04/FSG_Brochure_AW-Apr-2024.pdf',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-link-disclaimer-url',
                'label' => 'Footer Link: Disclaimer',
                'description' => 'URL for the Disclaimer link in the footer.',
                'type' => 'text',
                'value' => 'https://www.centrepointalliance.com.au/terms/',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-link-privacy-url',
                'label' => 'Footer Link: Privacy Policy',
                'description' => 'URL for the Privacy Policy link in the footer.',
                'type' => 'text',
                'value' => 'https://www.centrepointalliance.com.au/privacy/',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-link-about-url',
                'label' => 'Footer Link: About',
                'description' => 'URL for the About link in the footer.',
                'type' => 'text',
                'value' => 'https://www.jenbury.com.au/about',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-link-faq-url',
                'label' => 'Footer Link: FAQ',
                'description' => 'URL for the FAQ link in the footer.',
                'type' => 'text',
                'value' => 'https://www.jenbury.com.au/faqs',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
            [
                'parent' => 'Footer',
                'slug' => 'footer-link-contact-url',
                'label' => 'Footer Link: Contact',
                'description' => 'URL for the Contact link in the footer.',
                'type' => 'text',
                'value' => 'https://www.jenbury.com.au/contact',
                'previous_value' => null,
                'modified' => '2025-03-31 03:20:18',
            ],
        ];

        $table = $this->table('content_blocks');
        $table->insert($data)->saveData();
    }
}