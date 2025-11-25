<?php

namespace ElevateCommerce\VisualEditor\Console\Commands;

use Illuminate\Console\Command;
use ElevateCommerce\VisualEditor\Database\Seeders\AdminSeeder;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visual-editor:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a default admin user for Visual Editor';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Creating admin user...');
        
        $seeder = new AdminSeeder();
        $seeder->setCommand($this);
        $seeder->run();
    }
}

