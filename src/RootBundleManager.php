<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\{Attributes\ConfigValue, Attributes\Service, Interfaces\DirectoryCreator};

#[Service]
readonly class RootBundleManager
{
    public function __construct(
        #[ConfigValue(ConfigOptions\RootBundleDirectory::class)]
        private string   $directory,

        #[ConfigValue(ConfigOptions\RootBundleSource::class)]
        private string   $source,
        DirectoryCreator $directoryCreator,
    )
    {
        $directoryCreator->create($this->directory);
    }

    public function path(): string
    {
        $filename = $this->directory . '/cacert.pem';

        if (!file_exists($filename)) {
            file_put_contents($filename, file_get_contents($this->source));
        }

        return realpath($filename);
    }
}
