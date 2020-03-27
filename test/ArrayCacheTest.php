<?php

use Illuminate\Foundation\Testing\TestCase;

class ArrayCacheTest extends TestCase
{
    protected $levelOneKey = 'array-cache-test';
    protected $levelTwoKey = 'array-cache-test.level2';
    protected $value1 = 'I\'m cache number 1';
    protected $value2 = 'I\'m cache number 2';

    public function method_put_works()
    {
        \ArrayCache::put($this->levelOneKey, $this->value1);

        $this->assertEquals($this->value1, \Cache::get($this->levelOneKey));
    }
}