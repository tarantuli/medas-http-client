<?php

declare(strict_types=1);

namespace Medas\HttpClient;

readonly class Response
{
    public function __construct(
        public int    $code,
        public string $rawBody,
        public mixed  $body,
        public string $header,
        array         $curlInfo,
    )
    {
    }
}
