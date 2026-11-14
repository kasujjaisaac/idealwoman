<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('site:check')]
#[Description('Check production SEO, image processing, and enquiry configuration without sending email')]
class CheckSiteReadiness extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $checks = [
            'Production environment' => config('app.env') === 'production',
            'Debug output disabled' => ! config('app.debug'),
            'HTTPS application URL' => str_starts_with(config('app.url'), 'https://'),
            'Search indexing enabled' => (bool) config('idealwoman.indexable'),
            'PHP GD with WebP support' => function_exists('imagewebp'),
            'Responsive image manifest generated' => is_file(public_path('images/variants.json')),
            'Storage writable' => is_writable(storage_path('app')),
            'Enquiries enabled' => (bool) config('idealwoman.enquiries_enabled'),
            'SMTP transport configured' => config('mail.default') === 'smtp' && filled(config('mail.mailers.smtp.host')),
            'Enquiry recipient configured' => filter_var(config('idealwoman.enquiries_to'), FILTER_VALIDATE_EMAIL) !== false,
        ];
        foreach ($checks as $label => $passed) {
            $passed ? $this->info('PASS: '.$label) : $this->error('CHECK: '.$label);
        }
        $this->comment('Also verify a real enquiry reaches the inbox, submit the sitemap in Search Console, monitor /up, and configure off-server backups with a restore test.');

        return in_array(false, $checks, true) ? self::FAILURE : self::SUCCESS;
    }
}
