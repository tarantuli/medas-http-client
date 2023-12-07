<?php

declare(strict_types=1);

namespace Medas\HttpClient\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class RootBundleSource implements ConfigOption
{
    public function __construct(
        private Group $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'root-bundle-source';
    }

    public function description(): string
    {
        return 'The URL where the root bundle can be downloaded from';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): string
    {
        return 'https://curl.haxx.se/ca/cacert.pem';
    }
}
