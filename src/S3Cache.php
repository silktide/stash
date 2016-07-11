<?php


namespace Silktide\Stash;

use Aws\S3\S3Client;

class S3Cache implements SimpleCacheInterface
{
    use CacheHelperTrait;

    const BUCKET = "cache.silktide.com";

    protected $s3Client;
    protected $defaultTTL;

    public function __construct(S3Client $s3Client)
    {
        $this->s3Client = $s3Client;
        $this->defaultTTL = new \DateInterval("PT24H");
    }

    public function set($key, $value, $ttl = null)
    {
        try{
            $this->s3Client->putObject([
                "Bucket" => self::BUCKET,
                "Key" => $key,
                "Body" => $value, // AWS can handle if this is a resource natively
                "Expires" => $this->ttlToDateTime($ttl, $this->defaultTTL)
            ]);
        } catch(\Exception $e) {
            throw new \RuntimeException("Unable to set object in S3Cache '{$key}''");
        }
    }

    public function get($key)
    {
        $result = $this->s3Client->getObject([
            "Bucket" => self::BUCKET,
            "Key" => $key
        ]);

        return (string) $result['Body'];
    }

    public function exists($key)
    {
        return $this->s3Client->doesObjectExist(self::BUCKET, $key);
    }
}

