<?php

declare(strict_types=1);

namespace Medas\HttpClient\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ContentTypeNotImplemented extends BaseException
{
    public function __construct(string $contentType)
    {
        parent::__construct($contentType);
    }

    public function pattern(): string
    {
        return 'Casting content type %s to string is not implemented';
    }
}
