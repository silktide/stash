<?php


namespace Silktide\Stash;


interface SimpleCacheInterface
{
    /**
     * @param int|string $key
     * @param $value
     * @param \DateInterval|\DateTime|int|null $ttl
     */
    public function set($key, $value, $ttl = null);

    /**
     * @param int|string $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param int|string $key
     * @return boolean
     */
    public function exists($key);

}