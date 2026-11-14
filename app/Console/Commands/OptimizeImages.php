<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('images:optimize')]
#[Description('Generate responsive WebP photos and CMS uploads while preserving originals')]
class OptimizeImages extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ImageOptimizer $optimizer): int
    {
        $manifest = [];
        foreach (glob(public_path('images/*.jpg')) ?: [] as $source) {
            $name = pathinfo($source, PATHINFO_FILENAME);
            $manifest[$name] = $optimizer->generate($source, public_path('images'), $name);
            $this->line('Optimised '.$name);
        }
        $temporary = tempnam(public_path('images'), 'manifest-');
        try {
            file_put_contents($temporary, json_encode($manifest, JSON_THROW_ON_ERROR));
            rename($temporary, public_path('images/variants.json'));
            chmod(public_path('images/variants.json'), 0644);
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
        $disk = Storage::disk('local');
        foreach ($disk->files('cms') as $path) {
            if (! in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }
            $name = pathinfo($path, PATHINFO_FILENAME);
            $metadata = $optimizer->generate($disk->path($path), $disk->path('cms/variants'), $name);
            $disk->put('cms/variants/'.$name.'.json', json_encode($metadata, JSON_THROW_ON_ERROR));
            $this->line('Optimised upload '.$name);
        }

        return self::SUCCESS;
    }
}
