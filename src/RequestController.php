<?php

declare(strict_types=1);

namespace Medas\HttpClient;

use Medas\Core\Attributes\{ConfigValue, Service};

#[Service]
readonly class RequestController
{
    private \CurlHandle $handle;

    public function __construct(
        private BodyHandler        $bodyHandler,
        private HeaderHandler      $headerHandler,
        private CookieHandler      $cookieHandler,
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

        return $this->fetch();
    }

    private function setMethod(Request $request): void
    {
        curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, strtoupper($request->method));
    }

    private function setUrl(Request $request): void
    {
        if ($request->queryArguments) {
            $request->url .= (str_contains($request->url, '?') ? '&' : '?')
                . http_build_query($request->queryArguments);
        }

        curl_setopt($this->handle, CURLOPT_URL, $request->url);
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

    private function setTimeouts(Request $request): void
    {
        if ($request->connectionTimeout !== null) {
            curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT_MS, $request->connectionTimeout);
        }

        if ($request->totalRequestTimeout !== null) {
            curl_setopt($this->handle, CURLOPT_TIMEOUT_MS, $request->totalRequestTimeout);
        }
    }

    private function fetch(): Response
    {
        $response = curl_exec($this->handle);

        if ($response === false) {
            throw new Exceptions\CurlError(curl_errno($this->handle), curl_error($this->handle));
        }

        $info = curl_getinfo($this->handle);

        if ($info['http_code'] === 307) {
            $this->setUrl($info['url']);

            return $this->fetch();
        }

        return $this->responseController->create($response, $info);
    }

    private function setCookies(Request $request): void
    {
        curl_setopt(
            $this->handle,
            CURLOPT_COOKIE,
            $this->cookieHandler->toString($request->cookies)
        );
    }
}
