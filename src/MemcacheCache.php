<?php


namespace Silktide\Stash;


class MemcacheCache implements SimpleCacheInterface
{
    use CacheHelperTrait;

    protected $host;

    /**
     * @var \Memcached|null
     */
    protected $memcached = null;

    /**
     * @var \DateInterval
     */
    protected $defaultTtl;

    public function __construct($host)
    {
        $this->host = $host;
        $this->defaultTtl = new \DateInterval("PT24H");
    }

    public function set($key, $value, $ttl = null)
    {
        $this->load();
        $seconds = $this->ttlToDateTime($ttl, $this->defaultTtl)->getTimestamp() - time();
        $this->memcached->set($key, $value, $seconds);
    }

    public function get($key)
    {
        $this->load();
        return $this->memcached->get($key);
    }

    public function exists($key)
    {
        $this->load();
        return $this->exists($key);
    }

    protected function load()
    {
        if (!$this->memcached) {
            $this->memcached = new \Memcached($this->host);
        }
    }
}