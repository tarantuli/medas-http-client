<?php

declare(strict_types=1);

namespace Medas\HttpClient\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class CookieJarFile implements ConfigOption
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
        return 'cookie-jar-file';
    }

    public function description(): string
    {
        return 'The path to the file where cookies can be stored';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): null
    {
        return null;
    }
}
