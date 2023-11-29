<?php

declare(strict_types=1);

namespace Medas\HttpClient;

class Request
{
    public array $queryArguments = [];
    public array $headers = [];
    public array $cookies = [];
    public Body|null $body = null;
    public int|null /** milliseconds */ $connectionTimeout = null;
    public int|null /** milliseconds */ $totalRequestTimeout = null;

    public function __construct(
        public string $url,
        public string $method = 'GET',
    )
    {
    }
}
