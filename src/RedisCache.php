<?php


namespace Silktide\Stash;


use Predis\Client;

class RedisCache implements SimpleCacheInterface
{
    use CacheHelperTrait;

    /**
     * @var Client
     */
    protected $client;
    protected $host;
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
        $this->client->set($key, $value, "ex", $seconds);
    }

    public function get($key)
    {
        $this->load();
        return $this->client->get($key);
    }

    public function exists($key)
    {
        $this->load();
        return $this->client->exists($key);
    }

    public function delete($key)
    {
        $this->load();
        $this->client->del($key);
    }

    protected function load()
    {
        if (!$this->client) {
            $this->client = DependencyRegistry::load("RedisCache", function() {
                return new Client(["host" => $this->host]);
            });
        }
    }
}