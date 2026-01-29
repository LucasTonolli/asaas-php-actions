<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Support\Http\Interface;

use Psr\Http\Client\ClientInterface;

/**
 * Contract for HTTP Client Factories.
 *
 * Any implementation must provide a pre-configured PSR-18 client
 * ready to communicate with the Asaas API, including authentication
 * and base headers.
 */
interface HttpClientFactoryInterface
{
    /**
     * Creates and configures a PSR-18 compliant HTTP client.
     *
     * @return ClientInterface A configured client ready for requests.
     *
     * @throws \RuntimeException If the client cannot be instantiated due to missing dependencies.
     */
    public function create(): ClientInterface;
}
