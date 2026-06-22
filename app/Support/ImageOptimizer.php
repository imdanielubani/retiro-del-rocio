<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Optimises an uploaded image in place: caps the width, re-encodes at web
 * quality, and respects EXIF orientation. Keeps the original format/filename
 * so callers don't need to change stored paths. Uses GD (always available on
 * the production image). Any failure is reported but never blocks the upload.
 */
class ImageOptimizer
{
    public static function optimize(
        string $path,
        string $disk = 'public',
        int $maxWidth = 1920,
        int $quality = 82,
    ): void {
        try {
            if (! function_exists('imagecreatetruecolor')) {
                return; // GD not available
            }

            $full = Storage::disk($disk)->path($path);
            if (! is_file($full)) {
                return;
            }

            $info = @getimagesize($full);
            if (! $info) {
                return;
            }
            [$w, $h] = $info;
            $mime = $info['mime'] ?? '';

            $img = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($full),
                'image/png' => @imagecreatefrompng($full),
                'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($full) : null,
                default => null, // gif/svg/etc. — leave untouched
            };
            if (! $img) {
                return;
            }

            // Memory headroom for large photos.
            @ini_set('memory_limit', '512M');

            // Respect EXIF orientation on JPEGs so re-encoding never rotates them.
            if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
                $exif = @exif_read_data($full);
                $o = $exif['Orientation'] ?? 1;
                if ($o == 3) {
                    $img = imagerotate($img, 180, 0);
                } elseif ($o == 6) {
                    $img = imagerotate($img, -90, 0);
                } elseif ($o == 8) {
                    $img = imagerotate($img, 90, 0);
                }
                $w = imagesx($img);
                $h = imagesy($img);
            }

            // Downscale if wider than the cap.
            $dst = $img;
            if ($w > $maxWidth) {
                $nw = $maxWidth;
                $nh = (int) round($h * $maxWidth / $w);
                $dst = imagecreatetruecolor($nw, $nh);
                if ($mime === 'image/png') {
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                }
                imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
            }

            // Re-encode in place (keep format).
            match ($mime) {
                'image/png' => imagepng($dst, $full, 6),
                'image/webp' => imagewebp($dst, $full, $quality),
                default => imagejpeg($dst, $full, $quality),
            };
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
