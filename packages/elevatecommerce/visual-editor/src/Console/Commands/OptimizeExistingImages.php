<?php

namespace ElevateCommerce\VisualEditor\Console\Commands;

use Illuminate\Console\Command;
use ElevateCommerce\VisualEditor\Models\Media;
use ElevateCommerce\VisualEditor\Services\ImageOptimizer;

class OptimizeExistingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize all existing images (create WebP versions and responsive sizes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $optimizer = new ImageOptimizer();
        $images = Media::where('mime_type', 'like', 'image/%')->get();
        
        if ($images->isEmpty()) {
            $this->info('No images found to optimize.');
            return 0;
        }
        
        $this->info("Found {$images->count()} images to optimize...");
        $bar = $this->output->createProgressBar($images->count());
        $bar->start();
        
        $success = 0;
        $failed = 0;
        
        foreach ($images as $image) {
            try {
                $optimizer->optimize($image->path, $image->disk);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("Failed to optimize {$image->filename}: {$e->getMessage()}");
            }
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✓ Successfully optimized: {$success} images");
        if ($failed > 0) {
            $this->warn("✗ Failed to optimize: {$failed} images");
        }
        
        $this->newLine();
        $this->info('Image optimization complete!');
        $this->info('WebP versions and responsive sizes have been created.');
        
        return 0;
    }
}
