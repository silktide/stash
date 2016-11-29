<?php


namespace Silktide\Stash;


use Silktide\Stash\Exception\UnsupportedCacheException;

class LockParser
{
    protected $file;
    protected $packages = [];
    protected $loaded = false;

    protected function load()
    {
        if ($this->loaded) {
            return;
        }
        $this->loaded = true;
        $revertDir = getcwd();

        $cwd = __DIR__;
        chdir($cwd);
        while (chdir("../") && $cwd!= getcwd()) {
            $cwd = getcwd();
            if (file_exists("vendor") && file_exists("composer.lock")) {
                $this->file = getcwd() . "/composer.lock";
            }
        }

        chdir($revertDir);

        $decoded = json_decode(file_get_contents($this->file));
        if (json_last_error() === 0 && isset($decoded->packages)) {
            foreach ($decoded->packages as $package) {
                $this->packages[$package->name] = $package->version;
            }
        }
    }

    public function exists($key)
    {
        $this->load();
        return isset($this->packages[$key]);
    }

    public function getVersion($key)
    {
        $this->load();
        if (!$this->exists($key)) {
            throw new \Exception("The package '{$key}' is not installed");
        }

        return $this->packages[$key];
    }
}
