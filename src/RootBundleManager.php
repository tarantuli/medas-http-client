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
        // TODO: Implement pattern() method.
        $filename = $this->directory . '/cacert.pem';
        $path = realpath($filename);

        if ($path === false) {
            throw new Exceptions\CannotWriteToCacertPem($this->directory);
        }

        if (!file_exists($filename)) {
            $contents = file_get_contents($this->source);

            if ($contents === false) {
                throw new Exceptions\FailedToReadCacertSource($this->source);
            }

            file_put_contents($filename, $contents);
        }

        return $path;
    }
}
