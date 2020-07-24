<?php

declare(strict_types=1);

namespace TTBooking\EntityLocator;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class EntityLocatorServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/entity-locator.php' => $this->app->configPath('entity-locator.php'),
            ], 'config');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/entity-locator.php', 'entity-locator');

        $this->app->singleton(Contracts\EntityLocator::class, function () {
            return $this->app->make(AggregateLocator::class, [
                'locators' => $this->app['config']['entity-locator.locators'],
                'fallback' => $this->app['config']['entity-locator.enable_fallback'],
                'ancestralOrdering' => $this->app['config']['entity-locator.ancestral_ordering'],
            ]);
        });

        $this->app->extend(ModelLocator::class, function (ModelLocator $locator) {
            return new CompositeKeyLocator($locator, $this->app['config']['entity-locator.composite_delimiter']);
        });

        $this->app->extend(Contracts\EntityLocator::class, function (Contracts\EntityLocator $locator) {
            $aliases = $this->app['config']['entity-locator.aliases'];
            if ($this->app['config']['entity-locator.merge_with_morph_map']) {
                $override = $this->app['config']['entity-locator.override_morph_map'];
                $morphMap = Relation::morphMap();
                $aliases = $override ? $aliases + $morphMap : $morphMap + $aliases;
            }

            return new AliasLocator($locator, $aliases);
        });

        $this->app->alias(Contracts\EntityLocator::class, 'entityLocator');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Contracts\EntityLocator::class, 'entityLocator'];
    }
}
