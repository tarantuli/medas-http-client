<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\AsSingleton;
use Medas\Json\JsonPackage;
use Medas\ServiceManager\BasePackage;

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
