<?php


namespace Silktide\Stash;

use Aws\AwsClient;
use Aws\S3\S3Client;
use Silktide\Stash\Exception\UnsupportedCacheException;
use Silktide\Stash\S3\Version2Layer;
use Silktide\Stash\S3\Version3Layer;

class S3Cache implements SimpleCacheInterface
{
    use CacheHelperTrait;

    const BUCKET = "cache.silktide.com";

    protected $defaultTTL;

    /**
     * @var SimpleCacheInterface
     */
    protected $layer;

    public function __construct(LockParser $lockParser)
    {
        $this->defaultTTL = new \DateInterval("PT24H");

        $awsKey = "aws/aws-sdk-php";
        if (!$lockParser->exists($awsKey)) {
            throw new UnsupportedCacheException("No AWS SDK is installed");
        }


        $version = $lockParser->getVersion($awsKey);
        $explodedVersion = explode(".", $version);


        switch((int)$explodedVersion[0]){
            case 3:
                return new Version3Layer($this->defaultTTL);

            case 2:
                return new Version2Layer($this->defaultTTL);

            default:
                throw new UnsupportedCacheException("AWS SDK Version {$version} is not currently supported");
        }
    }

    public function get($key)
    {
        return $this->layer->get($key);
    }

    public function delete($key)
    {
        $this->layer->delete($key);
    }

    public function exists($key)
    {
        return $this->layer->exists($key);
    }

    public function set($key, $value, $ttl = null)
    {
        $this->layer->set($key, $value, $ttl);
    }
}

