<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Support\Http\Interface;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface ResponseHandlerInterface
 *
 * Defines the contract for processing raw PSR-7 HTTP responses into domain-specific
 * data structures or throwing specialized exceptions.
 */
interface ResponseHandlerInterface
{
    /**
     * Handles and validates an API response.
     *
     * If the response status code is successful (2xx), it parses the JSON body
     * into an associative array. Otherwise, it must throw a domain-specific
     * exception based on the HTTP status code.
     *
     * @param  ResponseInterface  $response  The raw PSR-7 response from the HTTP client.
     *                                       * @return array<string, mixed> The decoded JSON body.
     *
     * @throws \AsaasPhpSdk\Exceptions\Api\AuthenticationException When status is 401.
     * @throws \AsaasPhpSdk\Exceptions\Api\ValidationException When status is 400.
     * @throws \AsaasPhpSdk\Exceptions\Api\NotFoundException When status is 404.
     * @throws \AsaasPhpSdk\Exceptions\Api\RateLimitException When status is 429.
     * @throws \AsaasPhpSdk\Exceptions\Api\ApiException For 5xx errors or unexpected status codes.
     */
    public function handle(ResponseInterface $response): array;
}
