<?php

namespace App\Services;

use RuntimeException;

class ImageOptimizer
{
    /** @return array{width: int, height: int, version: string, variants: array<int, string>} */
    public function generate(string $source, string $directory, string $name): array
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            throw new RuntimeException('Image optimisation requires PHP GD with WebP support.');
        }
        $image = @imagecreatefromstring(file_get_contents($source));
        if ($image === false) {
            throw new RuntimeException('The image could not be decoded.');
        }
        if (function_exists('exif_read_data') && in_array(strtolower(pathinfo($source, PATHINFO_EXTENSION)), ['jpg', 'jpeg'], true)) {
            $orientation = (int) (@exif_read_data($source)['Orientation'] ?? 1);
            if (in_array($orientation, [2, 4, 5, 7], true)) {
                imageflip($image, IMG_FLIP_HORIZONTAL);
            }
            $angle = match ($orientation) {
                3, 4 => 180,
                5, 6 => -90,
                7, 8 => 90,
                default => 0,
            };
            if ($angle !== 0) {
                $image = imagerotate($image, $angle, 0);
            }
        }
        $width = imagesx($image);
        $height = imagesy($image);
        $variants = [];
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('The image directory is not writable.');
        }
        foreach (array_unique(array_map(fn (int $size): int => min($size, $width), [640, 960, 1600, 2560])) as $size) {
            $resized = imagecreatetruecolor($size, max(1, (int) round($height * $size / $width)));
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $size, imagesy($resized), $width, $height);
            $filename = $name.'-'.$size.'.webp';
            $temporary = tempnam($directory, 'image-');
            try {
                if (! imagewebp($resized, $temporary, 76) || ! rename($temporary, $directory.'/'.$filename)) {
                    throw new RuntimeException('The optimised image could not be saved.');
                }
                chmod($directory.'/'.$filename, 0644);
            } finally {
                if (is_file($temporary)) {
                    unlink($temporary);
                }
            }
            $variants[$size] = $filename;
        }

        return ['width' => $width, 'height' => $height, 'version' => substr(hash('sha256', hash_file('sha256', $source).'webp-76-v1'), 0, 12), 'variants' => $variants];
    }
}
