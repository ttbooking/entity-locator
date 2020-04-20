<?php

namespace Daniser\EntityResolver;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class EntityResolverServiceProvider extends ServiceProvider implements DeferrableProvider
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
                __DIR__.'/../config/entity-resolver.php' => $this->app->configPath('entity-resolver.php'),
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
        $this->mergeConfigFrom(__DIR__.'/../config/entity-resolver.php', 'entity-resolver');

        $this->app->singleton(Contracts\EntityResolver::class, function () {
            return $this->app->make(AggregateResolver::class, [
                'resolvers' => $this->app['config']['entity-resolver.resolvers'],
                'fallback' => $this->app['config']['entity-resolver.enable_fallback'],
                'ancestralOrdering' => $this->app['config']['entity-resolver.ancestral_ordering'],
            ]);
        });

        $this->app->extend(ModelResolver::class, function (ModelResolver $resolver) {
            return new CompositeKeyResolver($resolver, $this->app['config']['entity-resolver.composite_delimiter']);
        });

        $this->app->extend(Contracts\EntityResolver::class, function (Contracts\EntityResolver $resolver) {
            $aliases = $this->app['config']['entity-resolver.aliases'];
            if ($this->app['config']['entity-resolver.merge_with_morph_map']) {
                $override = $this->app['config']['entity-resolver.override_morph_map'];
                $morphMap = Relation::morphMap();
                $aliases = $override ? $aliases + $morphMap : $morphMap + $aliases;
            }

            return new AliasResolver($resolver, $aliases);
        });

        $this->app->alias(Contracts\EntityResolver::class, 'entityResolver');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Contracts\EntityResolver::class, 'entityResolver'];
    }
}
