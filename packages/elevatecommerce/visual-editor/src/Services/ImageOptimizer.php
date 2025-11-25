<?php

namespace ElevateCommerce\VisualEditor\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageOptimizer
{
    protected ImageManager $manager;
    
    // Responsive image sizes
    protected array $sizes = [
        'thumbnail' => 400,
        'medium' => 800,
        'large' => 1600,
    ];

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Optimize and create responsive versions of an image.
     *
     * @param string $sourcePath Full path to source image
     * @param string $disk Storage disk
     * @return array Paths to generated images
     */
    public function optimize(string $sourcePath, string $disk = 'public'): array
    {
        $fullPath = Storage::disk($disk)->path($sourcePath);
        $image = $this->manager->read($fullPath);
        
        $pathInfo = pathinfo($sourcePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        
        $generated = [
            'original' => $sourcePath,
            'webp' => [],
            'sizes' => [],
        ];

        // Get original dimensions
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // Generate WebP version of original (high quality, smaller size)
        $webpOriginalPath = "{$directory}/{$filename}.webp";
        $image->toWebp(85)->save(Storage::disk($disk)->path($webpOriginalPath));
        $generated['webp']['original'] = $webpOriginalPath;

        // Generate responsive sizes
        foreach ($this->sizes as $sizeName => $maxWidth) {
            // Only generate if original is larger
            if ($originalWidth > $maxWidth) {
                // Calculate proportional height
                $ratio = $maxWidth / $originalWidth;
                $newHeight = (int) round($originalHeight * $ratio);

                // Resize image
                $resized = $image->scale(width: $maxWidth);
                
                // Save as WebP
                $webpPath = "{$directory}/{$filename}-{$sizeName}.webp";
                $resized->toWebp(85)->save(Storage::disk($disk)->path($webpPath));
                
                $generated['webp'][$sizeName] = $webpPath;
                $generated['sizes'][$sizeName] = [
                    'width' => $maxWidth,
                    'height' => $newHeight,
                    'path' => $webpPath,
                ];
            }
        }

        return $generated;
    }

    /**
     * Delete all generated versions of an image.
     */
    public function deleteVersions(string $sourcePath, string $disk = 'public'): void
    {
        $pathInfo = pathinfo($sourcePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];

        // Delete WebP versions
        $patterns = [
            "{$directory}/{$filename}.webp",
            "{$directory}/{$filename}-thumbnail.webp",
            "{$directory}/{$filename}-medium.webp",
            "{$directory}/{$filename}-large.webp",
        ];

        foreach ($patterns as $pattern) {
            if (Storage::disk($disk)->exists($pattern)) {
                Storage::disk($disk)->delete($pattern);
            }
        }
    }

    /**
     * Get available sizes for an image.
     */
    public function getAvailableSizes(): array
    {
        return $this->sizes;
    }
}
