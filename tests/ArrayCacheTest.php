<?php

namespace Ryssbowh\ArrayCache\Tests;

use Ryssbowh\ArrayCache\ArrayCacheServiceProvider;
use Ryssbowh\ArrayCache\Facade\ArrayCache;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Cache;

class ArrayCacheTest extends TestCase
{
    protected $levelOneKey = 'array-cache-test';
    protected $levelTwoKey1 = 'array-cache-test.key1';
    protected $levelTwoKey2 = 'array-cache-test.key2';
    protected $value = 'I\'m a cache value';
    protected $anotherValue = 'I\'m another cache value';
    protected $defaultValue = 'I\'m a default cache value';
    protected $manyKeys = ['array-cache-test.key1', 'array-cache-test.key2'];
    protected $manyValues = [
        'array-cache-test.key1' => 'I\'m a cache value',
        'array-cache-test.key2' => 'I\'m another cache value'
    ];
    protected $unexistingKey = 'array-cache-test.i-dont-exist';

    protected function getPackageProviders($app)
    {
        return [
            ArrayCacheServiceProvider::class,
        ];
    }

    public function test_methods_put_get_and_pull()
    {
        ArrayCache::put($this->levelOneKey, $this->value);

        $this->assertEquals($this->value, Cache::get($this->levelOneKey));
        $this->assertEquals($this->value, ArrayCache::get($this->levelOneKey));
        $this->assertEquals($this->value, ArrayCache::pull($this->levelOneKey));
        $this->assertEquals(null, Cache::get($this->levelOneKey));

        ArrayCache::put($this->levelTwoKey1, $this->value);

        $this->assertEquals($this->value, Cache::get($this->levelTwoKey1));
        $this->assertEquals($this->value, ArrayCache::get($this->levelTwoKey1));
        $this->assertEquals($this->value, ArrayCache::pull($this->levelTwoKey1));
        $this->assertEquals(null, Cache::get($this->levelTwoKey1));

        $default = ArrayCache::get($this->unexistingKey, $this->defaultValue);
        $this->assertEquals($default, $this->defaultValue);
        $default = ArrayCache::pull($this->unexistingKey, $this->defaultValue);
        $this->assertEquals($default, $this->defaultValue);

        ArrayCache::put($this->levelTwoKey1, $this->value);
        ArrayCache::put($this->levelTwoKey2, $this->value);
    }

    public function test_methods_remember_and_forget()
    {
        $_this = $this;

        ArrayCache::remember($this->levelOneKey, 100, function () use ($_this) {
            return $this->value;
        });

        $this->assertEquals($this->value, Cache::get($this->levelOneKey));

        ArrayCache::forget($this->levelOneKey);

        $this->assertEquals(null, Cache::get($this->levelOneKey));
    }

    public function test_methods_forever_and_rememberForever()
    {
        $_this = $this;

        ArrayCache::rememberForever($this->levelOneKey, function () use ($_this) {
            return $_this->value;
        });

        $this->assertEquals($this->value, Cache::get($this->levelOneKey));

        ArrayCache::forget($this->levelOneKey);

        ArrayCache::forever($this->levelOneKey, $this->value);

        $this->assertEquals($this->value, Cache::get($this->levelOneKey));

        ArrayCache::forget($this->levelOneKey);
    }

    public function test_method_putMany_and_deleteMultiple()
    {
        ArrayCache::putMany($this->manyValues);

        $this->assertEquals($this->value, Cache::get($this->levelTwoKey1));
        $this->assertEquals($this->anotherValue, Cache::get($this->levelTwoKey2));

        ArrayCache::deleteMultiple($this->manyKeys);

        $this->assertEquals(null, Cache::get($this->levelTwoKey1));
        $this->assertEquals(null, Cache::get($this->levelTwoKey2));
    }

    public function test_forgetting_dotted_key()
    {
        ArrayCache::put($this->levelTwoKey1, $this->value);
        ArrayCache::put($this->levelTwoKey2, $this->anotherValue);
        ArrayCache::forget($this->levelOneKey);

        $this->assertEquals(null, Cache::get($this->levelTwoKey1));
        $this->assertEquals(null, Cache::get($this->levelTwoKey2));
    }
}