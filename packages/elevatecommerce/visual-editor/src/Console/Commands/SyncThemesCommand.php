<?php

namespace ElevateCommerce\VisualEditor\Console\Commands;

use Illuminate\Console\Command;
use ElevateCommerce\VisualEditor\Support\ThemeLoader;

class SyncThemesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visual-editor:sync-themes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync themes from resources/views/themes to database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Syncing themes from filesystem to database...');
        $this->newLine();

        $themeLoader = app('visual-editor.theme-loader');
        $synced = $themeLoader->syncThemesToDatabase();

        if (empty($synced)) {
            $this->warn('No themes found in resources/views/themes/');
            return self::SUCCESS;
        }

        foreach ($synced as $result) {
            $theme = $result['theme'];
            $wasCreated = $result['was_created'];

            if ($wasCreated) {
                $this->line("  <fg=green>✓</> Created theme: <fg=cyan>{$theme->name}</> (v{$theme->version})");
            } else {
                $this->line("  <fg=blue>↻</> Updated theme: <fg=cyan>{$theme->name}</> (v{$theme->version})");
            }
        }

        $this->newLine();
        $this->info('✓ Successfully synced ' . count($synced) . ' theme(s)');

        return self::SUCCESS;
    }
}
