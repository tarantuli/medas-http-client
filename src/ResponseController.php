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

    public function create(
        string $response,

        #[ArrayShape(CurlTransferInfoShape::TRANSFER_INFO_SHAPE)]
        array  $info
    ): Response
    {
        $responseCode = $info['http_code'];
        $header = $this->determineHeader($response, $info['header_size']);
        $rawBody = substr($response, $info['header_size']);
        $body = $this->bodyHandler->parseString($rawBody, $info['content_type']);

        return new Response($responseCode, $rawBody, $body, $header, $info);
    }

    private function determineHeader(string $response, $headerSize): string
    {
        $allHeaders = substr($response, 0, $headerSize);

        // Split on blank lines, take the last block
        $headerBlocks = preg_split('/\r\n\r\n/', rtrim($allHeaders, "\r\n"));

        return end($headerBlocks);
    }
}
