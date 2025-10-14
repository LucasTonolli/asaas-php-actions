<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidCallbackException;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

final class Callback extends AbstractStructuredValueObject
{
	private function __construct(
		public readonly string $successUrl,
		public readonly bool $autoRedirect = true
	) {}

	public static function create(string $successUrl, bool $autoRedirect = true): self
	{
		// Validate URL format
		if (!filter_var($successUrl, FILTER_VALIDATE_URL)) {
			throw new InvalidCallbackException("Invalid success URL");
		}

		// Validate HTTPS for security
		$scheme = parse_url($successUrl, PHP_URL_SCHEME);
		if (mb_strtolower((string) $scheme) !== 'https') {
			throw new InvalidCallbackException('Success URL must use HTTPS protocol');
		}

		return new self($successUrl, $autoRedirect);
	}

	public static function fromArray(array $data): self
	{
		return self::create(
			successUrl: $data['successUrl'] ?? throw new InvalidCallbackException('successUrl is required'),
			autoRedirect: $data['autoRedirect'] ?? true
		);
	}
}
