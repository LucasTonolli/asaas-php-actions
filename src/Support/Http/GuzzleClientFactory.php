<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Support\Http;

use AsaasPhpSdk\Config\AsaasConfig;
use AsaasPhpSdk\Support\Http\Interface\HttpClientFactoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A factory for creating a pre-configured Guzzle HTTP client.
 *
 * This class implements the HttpClientFactoryInterface to provide a Guzzle-backed
 * PSR-18 client. It centralizes SDK-specific configurations such as authentication
 * headers, timeouts, and resiliency middlewares (retries and logging).
 *
 * @internal This is an internal infrastructure component.
 */
final class GuzzleClientFactory implements HttpClientFactoryInterface
{
    /** @var int The maximum number of times to retry a failed request. */
    private const MAX_RETRIES = 3;

    /** @var int The base delay in milliseconds between retries. */
    private const RETRY_DELAY_MS = 1000;

    /**
     * GuzzleClientFactory constructor.
     *
     * @param AsaasConfig $config The SDK configuration context.
     */
    public function __construct(private readonly AsaasConfig $config) {}

    /**
     * Creates and configures a Guzzle implementation of ClientInterface.
     *
     * @return ClientInterface A fully configured PSR-18 compliant HTTP client.
     */
    public function create(): ClientInterface
    {
        $stack = HandlerStack::create();

        $stack->push(self::createRetryMiddleware());

        if ($this->config->isSandbox() && $this->config->isLogsEnabled()) {
            $stack->push(self::createLoggingMiddleware());
        }

        return new Client([
            'base_uri' => $this->config->getBaseUrl(),
            'timeout' => 30,
            'connect_timeout' => 10,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'access_token' => $this->config->getToken(),
                'User-Agent' => 'AsaasPhpSdk/1.0 PHP/' . phpversion(),
            ],
            'handler' => $stack,
            'http_errors' => false,
        ]);
    }

    /**
     * Creates the retry middleware for resilient API communication.
     *
     * Retries requests on connection errors or specific server-side status codes
     * (429, 500, 502, 503, 504) using a linear backoff strategy.
     *
     * @return callable(callable): callable
     */
    private static function createRetryMiddleware(): callable
    {
        return Middleware::retry(
            function (
                int $retries,
                RequestInterface $request,
                ?ResponseInterface $response = null,
                ?RequestException $exception = null
            ): bool {
                if ($retries >= self::MAX_RETRIES) {
                    return false;
                }

                if ($response && in_array($response->getStatusCode(), [429, 500, 502, 503, 504])) {
                    return true;
                }

                if ($exception instanceof \GuzzleHttp\Exception\ConnectException) {
                    return true;
                }

                return false;
            },
            function (int $retries): int {
                return $retries * self::RETRY_DELAY_MS;
            }
        );
    }

    /**
     * Creates a logging middleware for request debugging.
     *
     * Only active in Sandbox mode if logs are explicitly enabled in AsaasConfig.
     *
     * @return callable(callable): callable
     */
    private static function createLoggingMiddleware(): callable
    {
        return Middleware::mapRequest(function (RequestInterface $request): RequestInterface {
            $stream = $request->getBody();
            $body = (string) $stream;
            if ($stream->isSeekable()) {
                $stream->rewind();
            }
            error_log(sprintf(
                '[Asaas] %s %s {%s}',
                $request->getMethod(),
                $request->getUri(),
                $body
            ));

            return $request;
        });
    }
}
