<?php


namespace Silktide\Stash;


class MemoryCache implements SimpleCacheInterface
{
    use CacheHelperTrait;

    protected $cache = [];
    protected $defaultTtl;

    public function __construct()
    {
        $this->defaultTtl = new \DateInterval("PT24H");
    }

    public function set($key, $value, $ttl=null)
    {
        $this->cache[$key] = [
            "ttl" => $this->ttlToDateTime($ttl, $this->defaultTtl),
            "value" => $value
        ];
    }

    protected function checkForExpiry($key)
    {
        if (isset($this->cache[$key])) {
            /**
             * @var \DateTime $ttl
             */
            $ttl = $this->cache[$key]["ttl"];
            if ($ttl->getTimestamp() < time()) {
                unset($this->cache[$key]);
            }
        }
    }

    public function get($key)
    {
        $this->checkForExpiry($key);
        // We should probably validate $key is a string, an int, or if we want to do something sick involving serializing and hashing of arrays
        if (!isset($this->cache[$key])) {
            throw new \Exception("Requested key '{$key}' does not exist in cache");
        }

        return $this->cache[$key];
    }

    public function exists($key)
    {
        $this->checkForExpiry($key);
        return (isset($this->cache[$key]));
    }
    
    
}