<?php

declare(strict_types=1);

namespace Medas\HttpClient\Interfaces;

use Medas\HttpClient\{Request, Response};

/**
 * Executes an outgoing HTTP request and returns the response. Consumers depend on
 * this rather than the concrete curl-based RequestController so the transport can
 * be swapped - e.g. a recording stub in tests, so outgoing calls never leave the
 * process.
 */
interface RequestController
{
    public function execute(Request $request): Response;
}
