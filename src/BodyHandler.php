<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\Attributes\Service;

#[Service]
readonly class BodyHandler
{
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
                return http_build_query($body);

            case 'text/json':
            case 'application/json':
                return json_encode($body, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        }

        throw new Exceptions\ContentTypeNotImplemented($contentType);
    }

    public function parseString(string $rawBody, string $contentType): mixed
    {
        switch ($this->stripCharset($contentType)) {
            case 'application/json':
            case 'text/json':
                if (str_starts_with($rawBody, '$')) {
                    $rawBody = substr($rawBody, 1);
                }

                return json_decode($rawBody, true, flags: JSON_THROW_ON_ERROR);

            case 'application/x-www-form-urlencoded':
                parse_str($rawBody, $body);

                return $body;

            default:
                return $rawBody;
        }
    }

    private function stripCharset(string $contentType): string
    {
        if (false !== $pos = strpos($contentType, ';')) {
            $contentType = substr($contentType, 0, $pos);
        }

        return $contentType;
    }
}
