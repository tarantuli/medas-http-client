<?php

declare(strict_types=1);

namespace Medas\HttpClient\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CurlError extends BaseException
{
    public function __construct(int $code, string $message)
    {
        parent::__construct($code, $message);
    }

    public function pattern(): string
    {
        return 'cURL error: [%s] %s';
    }
}
