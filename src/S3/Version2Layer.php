<?php


namespace Silktide\Stash\S3;


use Aws\S3\S3Client;
use Silktide\Stash\CacheHelperTrait;
use Silktide\Stash\DependencyRegistry;
use Silktide\Stash\S3Cache;
use Silktide\Stash\SimpleCacheInterface;

class Version2Layer implements SimpleCacheInterface
{
    use CacheHelperTrait;

    /**
     * @var S3Client
     */
    protected $s3Client;

    protected $defaultTTL;
    
    public function __construct($defaultTTL)
    {
        $this->defaultTTL = $defaultTTL;
    }

    public function set($key, $value, $ttl = null)
    {
        $this->load();
        try{
            $this->s3Client->putObject([
                "Bucket" => S3Cache::BUCKET,
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
        $this->load();
        $result = $this->s3Client->getObject([
            "Bucket" => S3Cache::BUCKET,
            "Key" => $key
        ]);

        return (string) $result['Body'];
    }

    public function exists($key)
    {
        $this->load();
        return $this->s3Client->doesObjectExist(S3Cache::BUCKET, $key);
    }

    public function delete($key)
    {
        $this->load();
        $this->s3Client->deleteObject([
            "Bucket" => S3Cache::BUCKET,
            "Key" => $key
        ]);
    }

    protected function load()
    {
        if (!$this->s3Client) {
            $this->s3Client = DependencyRegistry::load("S3V2", function(){
                return S3Client::factory([
                    "region" => "us-east-1"
                ]);
            });
        }
    }
}