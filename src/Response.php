<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use JetBrains\PhpStorm\ArrayShape;

readonly class Response
{
    public function __construct(
        public int    $code,
        public string $rawBody,
        public mixed  $body,
        public string $header,

        #[ArrayShape(Curl::TRANSFER_INFO_SHAPE)]
        public array  $curlInfo,
    )
    {
    }
}
