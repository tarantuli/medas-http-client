<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\Attributes\Service;

#[Service]
readonly class CookieHandler
{
    public function toString(array $cookies): string
    {
        $string = '';

        foreach ($cookies as $key => $value) {
            $string .= $key . '=' . $value . '; ';
        }

        return substr($string, 0, -2);
    }
}
