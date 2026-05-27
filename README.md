# medas-http-client

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

A cURL-based HTTP client with automatic body serialisation/deserialisation, SSL certificate bundle management, cookie jar support, and configurable timeouts.

**Core classes:**

| Class               | Purpose                                                                                                      |
|---------------------|--------------------------------------------------------------------------------------------------------------|
| `Request`           | Value object describing a single HTTP request (URL, method, headers, cookies, body, timeouts, error flags)   |
| `Body`              | Request body with content and an optional `Content-Type` string                                              |
| `Response`          | Immutable result: `$code`, `$rawBody`, `$body` (auto-parsed), `$header`, `$curlInfo`                         |
| `RequestController` | Builds and executes cURL calls; one `CurlHandle` is reused for the lifetime of the service                   |
| `SimpleRequests`    | Convenience façade for `GET`, `POST`, `PUT`, `PATCH`, and `DELETE` without constructing a `Request` manually |

**Body serialisation** — `BodyHandler` serialises the request body and deserialises the response body automatically based on `Content-Type`:

| Content-Type                        | Serialised as                      | Deserialised as      |
|-------------------------------------|------------------------------------|----------------------|
| `application/json` / `text/json`    | JSON string via `JsonEncoder`      | Decoded PHP value    |
| `application/x-www-form-urlencoded` | `http_build_query()`               | `parse_str()` result |
| `string` body                       | Passed through as-is               | —                    |
| Other                               | `ContentTypeNotImplemented` thrown | Raw string           |

**SSL** — `RootBundleManager` merges the system CA bundle with any additional bundles configured via `additional-ca-bundles`, writing the combined PEM to a temp file. Certificate revocation checking is on by default and can be disabled per-request.

**Error handling** — by default, a `BadRequest` exception is thrown for 4xx responses and `InternalServerError` for 5xx. Both can be disabled per-request via `$throwExceptionOn4xx` / `$throwExceptionOn5xx`.

## Configuration options

| Option                                      | Default    | Description                                                      |
|---------------------------------------------|------------|------------------------------------------------------------------|
| `http-client.default-connection-timeout`    | `5000`     | Connection timeout in milliseconds                               |
| `http-client.default-total-request-timeout` | `10000`    | Total request timeout in milliseconds                            |
| `http-client.cookie-jar-file`               | `null`     | Path to a cookie jar file for persistent cookies across requests |
| `http-client.additional-ca-bundles`         | `[]`       | Paths to extra PEM CA bundle files merged with the system bundle |
| `http-client.root-bundle-directory`         | (temp dir) | Directory where the merged CA bundle PEM is written              |

## Usage

### Package developer context

Register the package and inject `SimpleRequests` or `RequestController`:

```php
use Medas\HttpClient\HttpClientPackage;

HttpClientPackage::instance();
```

**Simple GET request:**

```php
use Medas\HttpClient\SimpleRequests;
use Medas\Core\Attributes\Service;

#[Service]
readonly class WeatherService
{
    public function __construct(
        private SimpleRequests $http,
    ) {}

    public function getCurrent(string $city): array
    {
        $response = $this->http->get(
            'https://api.example.com/weather',
            ['city' => $city, 'units' => 'metric'],
        );

        // $response->body is already decoded from JSON
        return $response->body;
    }
}
```

**POST with a JSON body:**

```php
$response = $this->http->post(
    'https://api.example.com/orders',
    ['product_id' => 42, 'quantity' => 3],
    // 'application/json' is the default content type
);

$orderId = $response->body['id'];
```

**PUT / PATCH / DELETE:**

```php
$this->http->put('https://api.example.com/orders/7', ['quantity' => 5]);
$this->http->patch('https://api.example.com/orders/7', ['status' => 'shipped']);
$this->http->delete('https://api.example.com/orders/7');
```

**Full control with `Request`:**

```php
use Medas\HttpClient\{Body, Request, RequestController};
use Medas\Core\Attributes\Service;

#[Service]
readonly class ApiClient
{
    public function __construct(
        private RequestController $requestController,
    ) {}

    public function call(): Response
    {
        $request = new Request('https://api.example.com/data', 'POST');

        $request->headers['Authorization'] = 'Bearer ' . $this->token;
        $request->headers['Accept']        = 'application/json';
        $request->body                     = new Body(['key' => 'value'], 'application/json');
        $request->queryArguments           = ['version' => '2'];

        // Custom timeouts for this specific request (milliseconds)
        $request->connectionTimeout    = 2000;
        $request->totalRequestTimeout  = 8000;

        // Don't throw on 404 — handle it manually
        $request->throwExceptionOn4xx  = false;

        return $this->requestController->execute($request);
    }
}
```

**Checking the response:**

```php
$response = $this->requestController->execute($request);

echo $response->code;       // HTTP status code, e.g., 200
echo $response->rawBody;    // Raw response string
var_dump($response->body);  // Auto-parsed: array for JSON, string for other types
echo $response->header;     // Raw response header block
```

**Form-encoded POST:**

```php
use Medas\HttpClient\{Body, Request};

$request = new Request('https://example.com/login', 'POST');
$request->body = new Body(
    ['username' => 'alice', 'password' => 'secret'],
    'application/x-www-form-urlencoded',
);

$response = $this->requestController->execute($request);
```

**Sending cookies:**

```php
$request = new Request('https://example.com/profile');
$request->cookies['session_id'] = 'abc123';
$request->cookies['locale']     = 'nl';

$response = $this->requestController->execute($request);
```

**Disabling certificate revocation check** (e.g., for internal services on Windows):

```php
$request = new Request('https://internal.corp/api');
$request->enableCertificateRevocationCheck = false;

$response = $this->requestController->execute($request);
```

**Handling errors gracefully:**

```php
use Medas\HttpClient\Exceptions\{BadRequest, CurlError, InternalServerError};

try {
    $response = $this->http->get('https://api.example.com/resource/99');
} catch (BadRequest $e) {
    // 4xx response
    echo $e->response->code;    // e.g. 404
    echo $e->response->rawBody;
} catch (InternalServerError $e) {
    // 5xx response
    logger()->error('API server error', ['code' => $e->response->code]);
} catch (CurlError $e) {
    // Network-level failure
    logger()->error('cURL error', ['errno' => $e->errno, 'message' => $e->message]);
}
```

### Backend user context

**Configuring timeouts and the cookie jar:**

```yaml
http-client:
  default-connection-timeout: 3000
  default-total-request-timeout: 15000
  cookie-jar-file: var/http-cookies.txt
```

**Adding custom CA certificates** (e.g., for internal PKI or self-signed certs):

```yaml
http-client:
  additional-ca-bundles:
    - /etc/ssl/certs/internal-ca.pem
    - /etc/ssl/certs/partner-ca.pem
```

The additional bundles are appended to the system bundle, and the combined file is written once per process start. If the bundle directory is not writable, `CannotWriteToCacertPem` is thrown at startup.
