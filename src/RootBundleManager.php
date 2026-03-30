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
        if (false === $directory = realpath($this->directory)) {
            throw new Exceptions\CannotWriteToCacertPem($this->directory);
        }

        $path = $directory . '/cacert.pem';

        if (!file_exists($path)) {
            $contents = file_get_contents($this->source);

            if ($contents === false) {
                throw new Exceptions\FailedToReadCacertSource($this->source);
            }

            file_put_contents($path, $contents);
        }

        return $path;
    }
}
