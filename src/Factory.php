<?php


namespace Silktide\Stash;

class Factory
{

    public function createS3Cache()
    {
        return new S3Cache($this->getLockParser());
    }

    public function createMemcacheCache($host)
    {
        return new MemcacheCache($host);
    }

    public function createRedisCache($host)
    {
        return new RedisCache($host);
    }

    protected function getLockParser()
    {
        return DependencyRegistry::load("LockParser", function(){
            return new LockParser();
        });
    }

}