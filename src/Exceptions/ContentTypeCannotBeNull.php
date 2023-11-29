<?php

declare(strict_types=1);

namespace Medas\HttpClient\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ContentTypeCannotBeNull extends BaseException
{
    public function pattern(): string
    {
        return 'Content type cannot be null when sending a non-string body';
    }
}
