<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Support\Http\Interface;

/**
 * Interface HttpTransporterInterface
 *
 * Defines the contract for the SDK's transport layer. It abstracts the 
 * complexities of PSR-7 messaging, body streaming, and JSON encoding/decoding.
 */
interface HttpTransporterInterface
{
	/**
	 * Sends an HTTP request to the Asaas API and parses the response.
	 *
	 * Implementation should handle the transformation of the data array into 
	 * a valid PSR-7 stream and ensure the response is processed by a 
	 * ResponseHandler before returning.
	 *
	 * @param string $method The HTTP verb (GET, POST, PUT, DELETE).
	 * @param string $path The relative API endpoint path (e.g., 'customers').
	 * @param array<string, mixed> $data Associative array of data to be sent in the request body.
	 * * @return array<string, mixed> The decoded API response body.
	 *
	 * @throws \AsaasPhpSdk\Exceptions\Api\ApiException Or one of its specialized subtypes (4xx/5xx).
	 * @throws \Psr\Http\Client\ClientExceptionInterface If a network-level error occurs.
	 * @throws \JsonException If data encoding or response decoding fails.
	 */
	public function send(string $method, string $path, array $data): array;
}
