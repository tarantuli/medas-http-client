<?php

declare(strict_types=1);

namespace Medas\HttpClient;

readonly class Body
{
    public function __construct(
        public mixed       $content,
        public string|null $type = null,
    )
    {
    }
}
