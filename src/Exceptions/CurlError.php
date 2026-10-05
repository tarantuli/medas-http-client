<?php

declare(strict_types=1);

namespace Medas\HttpClient\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CurlError extends BaseException
{
    public function __construct(
        public readonly int $errorNumber,
        string              $message,
        string|null         $debugInformation = null,
    )
    {
        parent::__construct(
            $errorNumber,
            $message,
            $debugInformation ? "\n\nDebug information:\n" . $debugInformation : ''
        );
    }

    public function pattern(): string
    {
        return 'cURL error: [%s] %s%s';
    }

    public function isTimeout(): bool
    {
        return $this->errorNumber === CURLE_OPERATION_TIMEDOUT;
    }
}
