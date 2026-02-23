<?php

declare(strict_types=1);

namespace Medas\HttpClient\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CannotWriteToCacertPem extends BaseException
{
    public function __construct(string $directory)
    {
        parent::__construct($directory);
    }

    public function pattern(): string
    {
        return 'Cannot write to cacert.pem in "%s"';
    }
}
