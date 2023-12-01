<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\Attributes\Service;

#[Service]
readonly class SimpleRequests
{
    public function __construct(
        private RequestController $requestController,
    )
    {
    }

    public function get(string $url, array $queryArguments = []): Response
    {
        $request = new Request($url);
        $request->queryArguments = $queryArguments;

        return $this->requestController->execute($request);
    }

    public function post(string $url, mixed $body, string $contentType = 'application/json'): Response
    {
        $request = new Request($url, 'POST');
        $request->body = new Body($body, $contentType);

        return $this->requestController->execute($request);
    }

    public function delete(string $url, array $queryArguments = []): Response
    {
        $request = new Request($url, 'DELETE');
        $request->queryArguments = $queryArguments;

        return $this->requestController->execute($request);
    }
}
