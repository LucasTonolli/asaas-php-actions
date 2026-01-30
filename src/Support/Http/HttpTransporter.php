<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Support\Http;

use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;
use AsaasPhpSdk\Support\Http\Interface\ResponseHandlerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Internal component responsible for bridging the SDK domain and the HTTP protocol.
 *
 * It manages the creation of PSR-7 requests, handles body stream conversion,
 * and delegates execution to a PSR-18 compliant client.
 *
 * @internal This class is not part of the public API and may change without notice.
 */
final readonly class HttpTransporter implements HttpTransporterInterface
{
    /**
     * @param  ClientInterface  $client  The PSR-18 HTTP client implementation.
     * @param  RequestFactoryInterface  $requestFactory  PSR-17 factory to create requests.
     * @param  StreamFactoryInterface  $streamFactory  PSR-17 factory to create body streams.
     * @param  ResponseHandlerInterface  $responseHandler  The engine that parses and validates API responses.
     */
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private ResponseHandlerInterface $responseHandler
    ) {}

    /**
     * Sends an HTTP request and returns the parsed response.
     *
     * @param  string  $method  The HTTP verb (GET, POST, etc.).
     * @param  string  $path  The relative endpoint path (e.g., 'customers').
     * @param  array<string, mixed>  $data  Optional data to be sent as JSON in the request body.
     * @return array<string, mixed> The decoded JSON response from the API.
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If the request execution fails at the network level.
     * @throws \JsonException If the provided data cannot be encoded to JSON.
     * @throws \AsaasPhpSdk\Exceptions\Api\ApiException Or its subtypes, if the API returns an error.
     */
    public function send(string $method, string $path, array $data = []): array
    {
        $request = $this->requestFactory->createRequest($method, $path);

        if (! empty($data)) {
            $body = $this->streamFactory->createStream(json_encode($data, JSON_THROW_ON_ERROR));
            $request = $request->withBody($body);
        }

        $response = $this->client->sendRequest($request);

        return $this->responseHandler->handle($response);
    }
}
