<?php 

namespace Ryssbowh\ArrayCache;

use Closure;
use Illuminate\Support\Arr;

class ArrayCache
{
    /**
     * Defined cache keys
     * 
     * @var array
     */
    protected $keys;

    /**
     * Cache key for the keys
     * 
     * @var string
     */
    protected $cacheKey = 'array-cache-keys';

    /**
     * Constructor. Loads the defined keys
     */
    public function __construct()
    {
        $this->keys = \Cache::get($this->cacheKey) ?? [];
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param string  $key
     * @param mixed  $default
     * 
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return \Cache::get($key, $default);
    }

    /**
     * Retrieve an item from the cache and delete it.
     *
     * @param string $key
     * @param mixed  $default
     * 
     * @return mixed
     */
    public function pull(string $key, $default = null)
    {
        $this->forgetKey($key);

        return \Cache::pull($key, $default);
    }

    /**
     * Store an item in the cache.
     *
     * @param string  $key
     * @param mixed  $value
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     * 
     * @return bool
     */
    public function put(string $key, $value, $ttl = null)
    {
        $this->addKey($key);

        return \Cache::put($key, $value, $ttl);
    }

    /**
     * Alias for put
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     */
    public function set(string $key, $value, $ttl = null)
    {
        return $this->put($key, $value, $ttl);
    }

    /**
     * Remembers a closure for a defined amount of seconds
     * 
     * @param string  $key
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     * @param \Closure  $callback
     * 
     * @return mixed
     */
    public function remember(string $key, $ttl, Closure $value)
    {
        $this->addKey($key);

        return \Cache::remember($key, $ttl, $value);
    }

    /**
     * Remembers a closure forever
     * 
     * @param string  $key
     * @param Closure $value
     * 
     * @return mixed
     */
    public function rememberForever(string $key, Closure $value)
    {
        $this->addKey($key);

        return \Cache::rememberForever($key, $value);
    }

    /**
     * Alias for rememberForever
     *
     * @param string  $key
     * @param \Closure  $callback
     * 
     * @return mixed
     */
    public function sear(string $key, Closure $callback)
    {
        return $this->rememberForever($key, $callback);
    }

    /**
     * Remembers a value forever
     * 
     * @param string $key
     * @param mixed  $value
     * 
     * @return mixed
     */
    public function forever($key, $value)
    {
        $this->addKey($key);

        return \Cache::forever($key, $value);
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param  array  $keys
     * @return array
     */
    public function many(array $keys)
    {
        return \Cache::many($keys);
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param array $values
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     * 
     * @return bool
     */
    public function putMany(array $values, $ttl = null): bool
    {   
        $this->addKeys(array_keys($values));

        return \Cache::putMany($values, $ttl);
    }

    /**
     * Alias for putMany
     *
     * @param array $values
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     * 
     * @return bool
     */
    public function setMultiple(array $values, $ttl = null): bool
    {
        return $this->putMany($values, $ttl);
    }

    /**
     * Store multiple items in the cache indefinitely.
     *
     * @param array $values
     * 
     * @return bool
     */
    protected function putManyForever(array $values): bool
    {
        $this->addKeys(array_keys($values));

        return \Cache::putManyForever($values);
    }

    /**
     * Store an item in the cache if the key does not exist.
     *
     * @param string $key
     * @param mixed  $value
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     * 
     * @return bool
     */
    public function add(string $key, $value, $ttl): bool
    {   
        $this->addKey($key);

        return \Cache::add($key, $value, $ttl);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     */
    public function forget(string $key)
    {
        if (data_get($this->keys, $key)) {
            $this->performForget($key, data_get($this->keys, $key));
            $this->forgetKey($key);
        }
    }

    /**
     * Remove items from the cache.
     *
     * @param array $keys
     */
    public function deleteMultiple(array $keys)
    {
        foreach ($keys as $key) {
            $this->forget($key);
        }
    }

    /**
     * Forget all cache keys
     */
    public function clear()
    {
        foreach ($this->keys as $key) {
            $this->performForget($key, data_get($this->keys, $key));
        }
        $this->keys = [];
        $this->write();
    }

    /**
     * Adds a key to the key cache
     * 
     * @param string $key
     */
    protected function addKey(string $key)
    {
        if (!Arr::has($this->keys, $key)) {
            data_set($this->keys, $key, true);
            $this->write();
        }
    }

    /**
     * Adds keys to the key cache
     * 
     * @param array $keys
     */
    protected function addKeys(array $keys)
    {
        foreach ($keys as $key) {
            if (!Arr::has($this->keys, $key)) {
                data_set($this->keys, $key, true);
            }
        }
        $this->write();
    }

    /**
     * Writes the key cache
     */
    protected function write()
    {
        \Cache::forever($this->cacheKey, $this->keys);
    }

    /**
     * Perform a recursive forget on a dotted array
     * 
     * @param string $key
     * @param array $array
     */
    protected function performForget(string $key, $array)
    {
        if (!is_array($array)) {
            \Cache::forget($key);
            return;
        }
        foreach ($array as $newkey => $array) {
            $this->performForget($key.'.'.$newkey, $array);
        }
    }

    /**
     * Forgets a key and writes the key cache
     * 
     * @param string $key
     */
    protected function forgetKey(string $key)
    {
        Arr::forget($this->keys, $key);
        $this->write();
    }
}