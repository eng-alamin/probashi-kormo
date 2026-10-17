<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Site;
use Illuminate\Console\Command;

class SiteCheck extends Command
{
    protected $signature = 'site:check';

    protected $description = 'Check that the public contact details are filled in before going live';

    public function handle(): int
    {
        $problems = Site::problems();

        $required = [
            ['SITE_PHONE', 'phone'],
            ['SITE_EMAIL', 'email'],
            ['SITE_ADDRESS', 'address'],
            ['SITE_WHATSAPP', 'whatsapp'],
        ];

        $rows = [];
        foreach ($required as [$env, $key]) {
            $rows[] = [$env, isset($problems[$key]) ? 'MISSING' : 'OK', isset($problems[$key]) ? 'required' : (string) Site::setting($key)];
        }

        $embed = (string) Site::setting('map_embed');
        $rows[] = ['SITE_HOURS', Site::hours() !== null ? 'OK' : 'not set', 'optional (Contact page hides it)'];
        $rows[] = [
            'SITE_MAP_EMBED',
            str_starts_with($embed, 'https://www.google.com/maps/embed') ? 'OK' : ($embed === '' ? 'not set' : 'IGNORED'),
            'optional, must be a Google Maps embed link',
        ];
        $rows[] = ['SITE_LICENSE_NO', Site::licenseNo() !== null ? 'OK' : 'not set', 'optional (About page hides it)'];

        $this->table(['.env key', 'Status', 'Note / value'], $rows);

        if ($problems === []) {
            $this->info('All required contact details are set.');

            return self::SUCCESS;
        }

        $this->error('Fill in the MISSING keys in .env, then run: php artisan config:clear');

        return self::FAILURE;
    }
}
