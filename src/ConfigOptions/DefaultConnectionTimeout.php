<?php

declare(strict_types=1);

namespace Medas\HttpClient\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class DefaultConnectionTimeout implements ConfigOption
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
        return 'default-connection-timeout';
    }

    public function description(): string
    {
        return 'Default connection timeout in milliseconds. Applied when a Request does not specify its own connectionTimeout. Use 0 for no limit';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): int
    {
        return 0;
    }
}
