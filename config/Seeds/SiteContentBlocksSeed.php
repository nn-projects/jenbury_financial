<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * SiteContentBlocks seed.
 */
class SiteContentBlocksSeed extends AbstractSeed
{
    public function run(): void
    {
        $data = [
[
                'parent' => 'site',
                'slug' => 'site-name',
                'label' => 'Site Name',
                'description' => 'The name of the site, used in the title and logo alt text.',
                'type' => 'text',
                'value' => 'Jenbury Financial',
                'previous_value' => null,
                'modified' => '2025-03-31 03:38:26',
            ],
        ];

        $table = $this->table('content_blocks');
        $table->insert($data)->saveData();
    }
}