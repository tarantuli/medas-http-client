<?php

declare(strict_types=1);

namespace Medas\HttpClient\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class RootBundleDirectory implements ConfigOption
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
        return 'root-bundle-directory';
    }

    public function description(): string
    {
        return 'The directory where the root bundle can be found or stored';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): string
    {
        return 'var/cache';
    }
}
