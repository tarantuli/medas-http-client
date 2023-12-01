<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\Attributes\Service;

#[Service]
readonly class BodyHandler
{
    public function __construct(
        private Json\JsonEncoder $jsonEncoder,
    )
    {
    }

    public function toString(Body $body): string
    {
        if (is_string($body->content)) {
            return $body->content;
        }

        if ($body->type === null) {
            throw new Exceptions\ContentTypeCannotBeNull();
        }

        $contentType = $this->stripCharset($body->type);

        switch ($contentType) {
            case 'application/x-www-form-urlencoded':
                return http_build_query($body->content);

            case 'text/json':
            case 'application/json':
                return $this->jsonEncoder->encode($body->content);
        }

        throw new Exceptions\ContentTypeNotImplemented($contentType);
    }

    public function parseString(string $rawBody, string|null $contentType): mixed
    {
        switch ($this->stripCharset($contentType)) {
            case 'application/json':
            case 'text/json':
                if (str_starts_with($rawBody, '$')) {
                    $rawBody = substr($rawBody, 1);
                }

                return $this->jsonEncoder->decode($rawBody);

            case 'application/x-www-form-urlencoded':
                parse_str($rawBody, $body);

                return $body;

            default:
                return $rawBody;
        }
    }

    private function stripCharset(string|null $contentType): string|null
    {
        if ($contentType === null) {
            return null;
        }

        if (false !== $pos = strpos($contentType, ';')) {
            $contentType = substr($contentType, 0, $pos);
        }

        return $contentType;
    }
}
