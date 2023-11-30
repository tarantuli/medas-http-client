<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use JetBrains\PhpStorm\ArrayShape;
use Medas\Core\Attributes\Service;

#[Service]
readonly class ResponseController
{
    public function __construct(
        private BodyHandler $bodyHandler,
    )
    {
    }

    public function create(string $response, #[ArrayShape(Curl::TRANSFER_INFO_SHAPE)] array $info): Response
    {
        $responseCode = $info['http_code'];
        $header = substr($response, 0, $info['header_size'] - 4);
        $rawBody = substr($response, $info['header_size']);
        $body = $this->bodyHandler->parseString($rawBody, $info['content_type']);

        return new Response($responseCode, $rawBody, $body, $header, $info);
    }
}
