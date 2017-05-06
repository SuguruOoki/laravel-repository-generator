<?php

namespace OzanAkman\RepositoryGenerator;

use Illuminate\Support\ServiceProvider;

class RepositoryGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
        $this->registerCommands();
    }

    /**
     * Publish configuration.
     */
    protected function publishConfig()
    {
        $configPath = __DIR__ . '/../config/repository-generator.php';

        if (function_exists('config_path')) {
            $publishPath = config_path('repository-generator.php');
        } else {
            $publishPath = base_path('config/repository-generator.php');
        }

        // Publish config files
        $this->publishes([$configPath => $publishPath], 'config');
    }

    /**
     * Merges configs.
     *
     * @return void
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/repository-generator.php', 'repository-generator');
    }

    /**
     * Register package commands.
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\Generate::class,
            ]);
        }
    }
}
