<?php

namespace Ryssbowh\ArrayCache;

use Illuminate\Support\ServiceProvider;

class ArrayCacheServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('array-cache', ArrayCache::class);
    }
}
