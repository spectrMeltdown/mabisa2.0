<?php

namespace React\Cache;

use React\Promise;
use React\Promise\PromiseInterface;

class ArrayCache implements CacheInterface
{
    private $limit;
    private $data = array();
    private $expires = array();
    private $supportsHighResolution;

    /**
     * The `ArrayCache` provides an in-memory implementation of the [`CacheInterface`](#cacheinterface).
     *
     * ```php
     * $cache = new ArrayCache();
     *
     * $cache->set('foo', 'bar');
     * ```
     *
     * Its constructor accepts an optional `?int $limit` parameter to limit the
     * maximum number of entries to store in the LRU cache. If you add more
     * entries to this instance, it will automatically take care of removing
     * the one that was least recently used (LRU).
     *
     * For example, this snippet will overwrite the first value and only store
     * the last two entries:
     *
     * ```php
     * $cache = new ArrayCache(2);
     *
     * $cache->set('foo', '1');
     * $cache->set('bar', '2');
     * $cache->set('baz', '3');
     * ```
     *
     * This cache implementation is known to rely on wall-clock time to schedule
     * future cache expiration times when using any version before PHP 7.3,
     * because a monotonic time source is only available as of PHP 7.3 (`hrtime()`).
     * While this does not affect many common use cases, this is an important
     * distinction for programs that rely on a high time precision or on systems
     * that are subject to discontinuous time adjustments (time jumps).
     * This means that if you store a cache item with a TTL of 30s on PHP < 7.3
     * and then adjust your system time forward by 20s, the cache item may
     * expire in 10s. See also [`set()`](#set) for more details.
     *
     * @param int|null $limit maximum number of entries to store in the LRU cache
     */
    public function __construct($limit = null)
    {
        $this->limit = $limit;


        $this->supportsHighResolution = \function_exists('hrtime');
    }

    public function get($key, $default = null)
    {

        if (isset($this->expires[$key]) && $this->now() - $this->expires[$key] > 0) {
            unset($this->data[$key], $this->expires[$key]);
        }

        if (!\array_key_exists($key, $this->data)) {
            return Promise\resolve($default);
        }


        $value = $this->data[$key];
        unset($this->data[$key]);
        $this->data[$key] = $value;

        return Promise\resolve($value);
    }

    public function set($key, $value, $ttl = null)
    {

        unset($this->data[$key]);
        $this->data[$key] = $value;


        unset($this->expires[$key]);
        if ($ttl !== null) {
            $this->expires[$key] = $this->now() + $ttl;
            \asort($this->expires);
        }


        if ($this->limit !== null && \count($this->data) > $this->limit) {


            \reset($this->expires);
            $key = \key($this->expires);



            if ($key === null || $this->now() - $this->expires[$key] < 0) {
                \reset($this->data);
                $key = \key($this->data);
            }
            unset($this->data[$key], $this->expires[$key]);
        }

        return Promise\resolve(true);
    }

    public function delete($key)
    {
        unset($this->data[$key], $this->expires[$key]);

        return Promise\resolve(true);
    }

    public function getMultiple(array $keys, $default = null)
    {
        $values = array();

        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $default);
        }

        return Promise\all($values);
    }

    public function setMultiple(array $values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return Promise\resolve(true);
    }

    public function deleteMultiple(array $keys)
    {
        foreach ($keys as $key) {
            unset($this->data[$key], $this->expires[$key]);
        }

        return Promise\resolve(true);
    }

    public function clear()
    {
        $this->data = array();
        $this->expires = array();

        return Promise\resolve(true);
    }

    public function has($key)
    {

        if (isset($this->expires[$key]) && $this->now() - $this->expires[$key] > 0) {
            unset($this->data[$key], $this->expires[$key]);
        }

        if (!\array_key_exists($key, $this->data)) {
            return Promise\resolve(false);
        }


        $value = $this->data[$key];
        unset($this->data[$key]);
        $this->data[$key] = $value;

        return Promise\resolve(true);
    }

    /**
     * @return float
     */
    private function now()
    {
        return $this->supportsHighResolution ? \hrtime(true) * 1e-9 : \microtime(true);
    }
}
