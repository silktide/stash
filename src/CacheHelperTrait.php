<?php


namespace Silktide\Stash;


trait CacheHelperTrait
{
    public function ttlToDateTime($ttl, \DateInterval $default)
    {
        if (is_null($default)) {
            throw new \InvalidArgumentException("Default TTL must be set");
        }

        if ($ttl instanceof \DateTime) {
            return $ttl;
        }

        $dateTime = new \DateTime();
        
        if ($ttl instanceof \DateInterval) {
            $dateTime->add($ttl);
        } elseif (is_numeric($ttl)) {
            $dateTime->setTimestamp(time() + $ttl);
        } elseif (is_null($ttl)) {
            $dateTime->add($default);
        } else {
            throw new \InvalidArgumentException("TTL value is invalid");
        }

        return $dateTime;
    }
}