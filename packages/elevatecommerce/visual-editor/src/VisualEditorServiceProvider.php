<?php

namespace ElevateCommerce\VisualEditor;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use ElevateCommerce\VisualEditor\Support\NavigationRegistry;

class VisualEditorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the navigation registry as a singleton
        $this->app->singleton('visual-editor.navigation', function () {
            return new \ElevateCommerce\VisualEditor\Support\NavigationRegistry();
        });

        // Register the dashboard registry as a singleton
        $this->app->singleton('visual-editor.dashboard', function () {
            return new \ElevateCommerce\VisualEditor\Support\DashboardRegistry();
        });

        // Register the context registry as a singleton
        $this->app->singleton('visual-editor.context', function () {
            return new \ElevateCommerce\VisualEditor\Support\ContextRegistry();
        });

        // Register the section registry as a singleton
        $this->app->singleton('visual-editor.section', function () {
            return new \ElevateCommerce\VisualEditor\Support\SectionRegistry();
        });

        // Register the theme loader as a singleton
        $this->app->singleton('visual-editor.theme-loader', function () {
            return new \ElevateCommerce\VisualEditor\Support\ThemeLoader();
        });

        // Register the page render service as a singleton
        $this->app->singleton(\ElevateCommerce\VisualEditor\Services\PageRenderService::class, function ($app) {
            return new \ElevateCommerce\VisualEditor\Services\PageRenderService(
                $app->make('visual-editor.section')
            );
        });

        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/visual-editor.php', 'visual-editor'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure admin auth guard
        $this->configureAuth();

        // Load themes based on context
        $this->loadThemes();

        // Register default navigation items
        $this->registerNavigation();

        // Register default dashboard components
        $this->registerDashboardComponents();

        // Load routes (single entry point)
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'visual-editor');

        // Register Blade components
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/admin/components', 'visual-editor');
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'visual-editor');
        Blade::component('tags-input', \ElevateCommerce\VisualEditor\View\Components\TagsInput::class);

        // Publish config
        $this->publishes([
            __DIR__.'/../config/visual-editor.php' => config_path('visual-editor.php'),
        ], 'visual-editor-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/visual-editor'),
        ], 'visual-editor-views');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'visual-editor-migrations');

        // Publish assets (JS/Vue components)
        $this->publishes([
            __DIR__.'/../public/build' => public_path('vendor/visual-editor'),
        ], 'visual-editor-assets');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \ElevateCommerce\VisualEditor\Console\Commands\SyncThemesCommand::class,
                \ElevateCommerce\VisualEditor\Console\Commands\OptimizeExistingImages::class,
            ]);
        }
    }

    /**
     * Configure the admin authentication guard.
     */
    protected function configureAuth(): void
    {
        config([
            'auth.guards.admin' => [
                'driver' => 'session',
                'provider' => 'admins',
            ],
            'auth.providers.admins' => [
                'driver' => 'eloquent',
                'model' => \ElevateCommerce\VisualEditor\Models\Admin::class,
            ],
            'auth.passwords.admins' => [
                'provider' => 'admins',
                'table' => 'admin_password_reset_tokens',
                'expire' => 60,
                'throttle' => 60,
            ],
        ]);
    }

    /**
     * Register default navigation items.
     */
    protected function registerNavigation(): void
    {
        $navigation = app('visual-editor.navigation');

        // Top-level: Dashboard
        $navigation->register('dashboard', [
            'label' => 'Dashboard',
            'url' => '/admin/dashboard',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
            'order' => 1,
        ]);

        // Group: Online store (Themes + Pages + Collections)
        $navigation->register('online-store', [
            'label' => 'Online store',
            'icon' => '  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />',
            'order' => 2,
            'children' => [
                'themes' => [
                    'label' => 'Themes',
                    'url' => '/admin/themes',
                    'order' => 1,
                ],
                'pages' => [
                    'label' => 'Pages',
                    'url' => '/admin/pages',
                    'order' => 2,
                ],
                'collections' => [
                    'label' => 'Collections',
                    'url' => '/admin/collections',
                    'order' => 3,
                ],
            ],
        ]);

        // Group: Content (Files + Inquiries)
        $navigation->register('content', [
            'label' => 'Content',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />',
            'order' => 3,
            'children' => [
                'files' => [
                    'label' => 'Files',
                    'url' => '/admin/media',
                    'order' => 1,
                ],
                'inquiries' => [
                    'label' => 'Inquiries',
                    'url' => '/admin/inquiries',
                    'order' => 2,
                ],
            ],
        ]);
    }

    /**
     * Register default dashboard components.
     */
    protected function registerDashboardComponents(): void
    {
        $dashboard = app('visual-editor.dashboard');

        // Welcome card
        $dashboard->register('welcome', [
            'view' => 'visual-editor::admin.components.welcome-card',
            'data' => function () {
                return [
                    'message' => 'Welcome to Visual Editor. This is your admin dashboard where you can manage your content.',
                ];
            },
            'width' => 'full',
            'order' => 1,
            'title' => 'Welcome',
        ]);

        // Example stat cards
        $dashboard->registerMany([
            'stat-users' => [
                'view' => 'visual-editor::admin.components.stat-card',
                'data' => [
                    'label' => 'Total Users',
                    'value' => 0,
                    'icon' => '👥',
                    'color' => 'blue',
                ],
                'width' => 'third',
                'order' => 10,
            ],
            'stat-content' => [
                'view' => 'visual-editor::admin.components.stat-card',
                'data' => [
                    'label' => 'Content Items',
                    'value' => 0,
                    'icon' => '📝',
                    'color' => 'green',
                ],
                'width' => 'third',
                'order' => 11,
            ],
            'stat-activity' => [
                'view' => 'visual-editor::admin.components.stat-card',
                'data' => [
                    'label' => 'Recent Activity',
                    'value' => 0,
                    'icon' => '📊',
                    'color' => 'purple',
                ],
                'width' => 'third',
                'order' => 12,
            ],
        ]);
    }

    /**
     * Load themes based on request context.
     */
    protected function loadThemes(): void
    {
        $themeLoader = app('visual-editor.theme-loader');

        // For frontend: load active theme only
        if (!request()->is('admin/*')) {
            $themeLoader->loadActiveTheme();
            return;
        }

        // For admin theme editor: load specific theme being edited
        if (request()->is('admin/themes/*/edit') || request()->is('admin/pages/*')) {
            // Extract theme slug from URL or session
            $themeSlug = session('editing_theme') ?? request()->segment(3);
            if ($themeSlug) {
                $themeLoader->loadThemeForEditing($themeSlug);
            }
            return;
        }

        // For theme list page: don't load any sections (just metadata)
        // No need to load sections when just viewing theme list
    }
}
