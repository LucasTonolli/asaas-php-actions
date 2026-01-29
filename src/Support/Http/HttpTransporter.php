<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Support\Http;

use AsaasPhpSdk\Support\Http\Interface\ResponseHandlerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @internal
 */
final readonly class HttpTransporter
{
	public function __construct(
		private ClientInterface $client,
		private RequestFactoryInterface $requestFactory,
		private StreamFactoryInterface $streamFactory,
		private ResponseHandlerInterface $responseHandler
	) {}

	public function send(string $method, string $path, array $data = []): array
	{
		$request = $this->requestFactory->createRequest($method, $path);

		if (!empty($data)) {
			$body = $this->streamFactory->createStream(json_encode($data, JSON_THROW_ON_ERROR));
			$request = $request->withBody($body);
		}

		$response = $this->client->sendRequest($request);

		return $this->responseHandler->handle($response);
	}
}
