<?php

declare(strict_types=1);

namespace Medas\HttpClient\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\HttpClient\{Response, ResponseCodes};

class InternalServerError extends BaseException
{
    public function __construct(Response $response)
    {
        parent::__construct(
            $response->code,
            ResponseCodes::RESPONSE_TEXTS[$response->code],
            $response->body
        );
    }

    public function pattern(): string
    {
        return '%u %s: %s';
    }
}
