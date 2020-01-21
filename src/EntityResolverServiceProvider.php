<?php

namespace Daniser\EntityResolver;

use Illuminate\Contracts\Support\DeferrableProvider;
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

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/entity-resolver.php', 'entity-resolver');

        $this->app->singleton(Contracts\EntityResolver::class, function () {
            return $this->app->make(AggregateResolver::class, [
                'resolvers' => $this->app['config']['entityResolver.resolvers'],
            ]);
        });

        $this->app->extend(Contracts\EntityResolver::class, function (Contracts\EntityResolver $resolver) {
            return new AliasResolver($resolver, $this->app['config']['entityResolver.aliases']);
        });

        $this->app->alias(Contracts\EntityResolver::class, 'entityResolver');
    }

    public function provides()
    {
        return [Contracts\EntityResolver::class, 'entityResolver'];
    }
}
