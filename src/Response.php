<?php

declare(strict_types=1);

namespace Medas\HttpClient;

readonly class Response
{
    public function __construct(
        public int         $code,
        public string      $body,
        public string|null $header,
        array              $curlInfo,
    )
    {
    }
}
