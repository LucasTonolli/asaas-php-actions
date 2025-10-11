<?php

namespace AsaasPhpSdk\ValueObjects\Base;

abstract class AbstractSimpleValueObject
{
	protected readonly string $value;

	/**
	 * Private constructor to enforce immutability and the factory pattern.
	 *
	 * @internal Should only be called from a static factory method like `from()`.
	 */
	protected function __construct(string $value)
	{
		$this->value = $value;
	}

	/**
	 * Gets the raw, underlying string value.
	 */
	public function value(): string
	{
		return $this->value;
	}

	public function equals(self $other): bool
	{
		return $other instanceof static && $this->value === $other->value;
	}
}
