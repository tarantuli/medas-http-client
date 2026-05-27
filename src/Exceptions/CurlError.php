<?php

declare(strict_types=1);

namespace Medas\HttpClient\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CurlError extends BaseException
{
    public function __construct(int $code, string $message, string|null $debugInformation = null)
    {
        parent::__construct($code, $message, $debugInformation);
    }

    public function pattern(): string
    {
        return 'cURL error: [%s] %s  %s';
    }
}
