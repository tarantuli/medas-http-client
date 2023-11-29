<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\AsSingleton;
use Medas\ServiceManager\BasePackage;

class HttpClientPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
