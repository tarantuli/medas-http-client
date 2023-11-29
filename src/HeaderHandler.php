<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\Attributes\Service;

#[Service]
readonly class HeaderHandler
{
    public function compile(array $values): array
    {
        $header = [];

        foreach ($values as $name => $value) {
            $header[] = sprintf('%s: %s', $name, $value);
        }

        return $header;
    }
}
