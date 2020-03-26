<?php

namespace Ryssbowh\ArrayCache;

use Illuminate\Support\ServiceProvider;
use Ryssbowh\ArrayCache\ArrayCache;

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
