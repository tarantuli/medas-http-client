<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\{AsSingleton, BasePackage};
use Medas\Json\JsonPackage;

class HttpClientPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            JsonPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
