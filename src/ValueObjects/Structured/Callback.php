<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidCallbackException;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

/**
 * A Value Object representing callback settings for a transaction.
 *
 * This class encapsulates the URL to which a user should be redirected after a
 * successful payment. It ensures the URL is valid and secure (HTTPS).
 */
final readonly class Callback extends AbstractStructuredValueObject
{
    /**
     * Callback private constructor.
     *
     * @internal
     *
     * @param  string  $successUrl  The secure URL for redirection upon success.
     * @param  bool  $autoRedirect  Whether to automatically redirect the user.
     */
    private function __construct(
        public string $successUrl,
        public bool $autoRedirect = true
    ) {}

    /**
     * Creates a new Callback instance with explicit, validated parameters.
     *
     * This factory validates the URL format and enforces the use of the HTTPS protocol.
     *
     * @param  string  $successUrl  The URL for successful payment redirection.
     * @param  bool  $autoRedirect  Whether to automatically redirect the user.
     * @return self A new, validated Callback instance.
     *
     * @throws InvalidCallbackException If the success URL is not a valid HTTPS URL.
     */
    private static function create(string $successUrl, bool $autoRedirect = true): self
    {

        if (! filter_var($successUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidCallbackException('Invalid success URL');
        }

        $scheme = parse_url($successUrl, PHP_URL_SCHEME);
        if (strtolower((string) $scheme) !== 'https') {
            throw new InvalidCallbackException('Success URL must use HTTPS protocol');
        }

        return new self($successUrl, $autoRedirect);
    }

    /**
     * Creates a Callback instance from a raw data array.
     *
     * @param  array{successUrl?: string, autoRedirect?: bool}  $data  The raw data array.
     * @return self A new, validated Callback instance.
     *
     * @throws InvalidCallbackException If required keys are missing or data types are incorrect.
     */
    public static function fromArray(array $data): self
    {
        if (! \array_key_exists('successUrl', $data)) {
            throw new InvalidCallbackException('successUrl is required');
        }

        return self::create(
            ...$data,
        );
    }
}
