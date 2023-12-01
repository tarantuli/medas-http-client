<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\{Attributes\ConfigValue, Attributes\Service, Interfaces\DirectoryManager};

#[Service]
readonly class RootBundleManager
{
    public function __construct(
        #[ConfigValue(ConfigOptions\RootBundleDirectory::class)]
        private string   $directory,
        DirectoryManager $directoryManager,
    )
    {
        $directoryManager->create($this->directory);
    }

    public function path(): string
    {
        $filename = $this->directory . '/cacert.pem';

        if (!file_exists($filename)) {
            file_put_contents($filename, file_get_contents('https://curl.haxx.se/ca/cacert.pem'));
        }

        return realpath($filename);
    }
}
