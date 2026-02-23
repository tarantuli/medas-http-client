<?php

declare(strict_types=1);

namespace Medas\HttpClient\Exceptions;

use Medas\Core\Exceptions\BaseException;

class FailedToReadCacertSource extends BaseException
{
    public function __construct(string $source)
    {
        parent::__construct($source);
    }

    public function pattern(): string
    {
        return 'Failed to read cacert source "%s"';
    }
}
