<?php

declare(strict_types=1);

namespace Medas\HttpClient\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class AdditionalCaBundles implements ConfigOption
{
    public function __construct(
        private HttpClientConfigGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'additional-ca-bundles';
    }

    public function description(): string
    {
        return 'A list of additional CA bundles to use in addition to the system default';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): array
    {
        return [];
    }
}
