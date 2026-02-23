<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\Attributes\{ConfigValue, Entrypoint, Service};

#[Service, Entrypoint]
readonly class RequestController
{
    private \CurlHandle $handle;

    public function __construct(
        private BodyHandler        $bodyHandler,
        private CookieHandler      $cookieHandler,
        private HeaderHandler      $headerHandler,
        private ResponseController $responseController,
        RootBundleManager          $rootBundleManager,

        #[ConfigValue(ConfigOptions\CookieJarFile::class)]
        string|null                $cookieJarFile,
    )
    {
        $this->handle = curl_init();

        curl_setopt($this->handle, CURLOPT_HEADER, true);
        curl_setopt($this->handle, CURLINFO_HEADER_OUT, true);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->handle, CURLOPT_ENCODING, '');

        // SSL
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, 2);

        if ($cookieJarFile !== null) {
            curl_setopt($this->handle, CURLOPT_COOKIEFILE, $cookieJarFile);
            curl_setopt($this->handle, CURLOPT_COOKIEJAR, $cookieJarFile);
        }

        curl_setopt($this->handle, CURLOPT_CAINFO, $rootBundleManager->path());
    }

    public function __destruct()
    {
        curl_close($this->handle);
    }

    public function execute(Request $request): Response
    {
        $this->setMethod($request);
        $this->setUrl($request);
        $this->setBody($request);
        $this->setHeader($request);
        $this->setCookies($request);
        $this->setTimeouts($request);

        $response = $this->fetch();

        $this->checkForErrors($request, $response);

        return $response;
    }

    private function setMethod(Request $request): void
    {
        curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, strtoupper($request->method));
    }

    private function setUrl(Request $request): void
    {
        $url = $request->url;

        if ($request->queryArguments) {
            $url .= (str_contains($url, '?') ? '&' : '?')
                . http_build_query($request->queryArguments);
        }

        curl_setopt($this->handle, CURLOPT_URL, $url);
    }

    private function setBody(Request $request): void
    {
        if ($request->body === null) {
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, null);

            return;
        }

        if ($request->body->type) {
            $request->headers['Content-Type'] = $request->body->type;
        }

        curl_setopt(
            $this->handle,
            CURLOPT_POSTFIELDS,
            $this->bodyHandler->toString($request->body)
        );
    }

    private function setHeader(Request $request): void
    {
        curl_setopt(
            $this->handle,
            CURLOPT_HTTPHEADER,
            $this->headerHandler->compile($request->headers)
        );
    }

    private function setCookies(Request $request): void
    {
        // Set to '' when no cookies need to be set
        curl_setopt(
            $this->handle,
            CURLOPT_COOKIE,
            $this->cookieHandler->toString($request->cookies)
        );
    }

    private function setTimeouts(Request $request): void
    {
        curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT_MS, $request->connectionTimeout ?? 0);
        curl_setopt($this->handle, CURLOPT_TIMEOUT_MS, $request->totalRequestTimeout ?? 0);
    }

    private function fetch(): Response
    {
        $response = curl_exec($this->handle);

        if ($response === false) {
            throw new Exceptions\CurlError(curl_errno($this->handle), curl_error($this->handle));
        }

        $info = curl_getinfo($this->handle);

        return $this->responseController->create($response, $info);
    }

    private function checkForErrors(Request $request, Response $response): void
    {
        if ($request->throwExceptionOn4xx && $response->code >= 400 && $response->code <= 499) {
            throw new Exceptions\BadRequest($response);
        }

        if ($request->throwExceptionOn5xx && $response->code >= 500 && $response->code <= 599) {
            throw new Exceptions\InternalServerError($response);
        }
    }
}
