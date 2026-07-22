<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\{
    Attributes\ConfigValue,
    Attributes\Service,
    Exceptions\FailedToReadContent,
    Interfaces\DirectoryCreator
};

#[Service]
readonly class RootBundleManager
{
    /** @var string[] */
    private array $additionalCaBundles;

    public function __construct(
        #[ConfigValue(ConfigOptions\RootBundleDirectory::class)]
        private string   $directory,

        #[ConfigValue(ConfigOptions\RootBundleSource::class)]
        private string   $source,

        #[ConfigValue(ConfigOptions\AdditionalCaBundles::class)]
        array|string     $additionalCaBundles,
        DirectoryCreator $directoryCreator,
    )
    {
        $this->additionalCaBundles = is_array($additionalCaBundles)
            ? $additionalCaBundles
            : explode(',', $additionalCaBundles);

        $directoryCreator->create($this->directory);
    }

    public function path(): string
    {
        if (false === $directory = realpath($this->directory)) {
            throw new Exceptions\CannotWriteToCacertPem($this->directory);
        }

        $path = $directory . '/cacert.pem';
        $needsRefresh = !file_exists($path) || filemtime($path) < time() - 7 * 24 * 60 * 60;

        if ($needsRefresh) {
            $contents = $this->getContents();

            if ($contents === false) {
                if (!file_exists($path)) {
                    throw new Exceptions\FailedToReadCacertSource($this->source);
                }

                // File exists, but refresh failed — keep the old one silently
                // Touch the file to make sure it's not checked again on the next request
                touch($path);
            }
            else {
                if (false === file_put_contents($path, $contents)) {
                    throw new Exceptions\CannotWriteToCacertPem($this->directory);
                }
            }
        }

        return $path;
    }

    private function getContents(): string|false
    {
        $sourceContent = file_get_contents($this->source);

        if ($sourceContent === false) {
            return false;
        }

        foreach ($this->additionalCaBundles as $additionalCaBundle) {
            $additionalContent = file_get_contents($additionalCaBundle);

            if ($additionalContent === false) {
                throw new FailedToReadContent($additionalCaBundle, 'unknown error');
            }

            $sourceContent .= $additionalContent;
        }

        return $sourceContent;
    }
}
