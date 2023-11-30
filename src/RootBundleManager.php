<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\Core\Interfaces\DirectoryManager;

#[Service]
readonly class RootBundleManager
{
    public function __construct(
        #[ConfigValue(ConfigOptions\RootBundleDirectory::class)]
        private string           $directory,

        private DirectoryManager $directoryManager,
    )
    {
    }

    public function path(): string
    {
        $filename = $this->directory . '/cacert.pem';

        if (!file_exists($filename)) {
            $this->directoryManager->create($filename);
            file_put_contents($filename, file_get_contents('https://curl.haxx.se/ca/cacert.pem'));
        }

        return $filename;
    }
}
