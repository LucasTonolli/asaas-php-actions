<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\ValueObjects\Structured\Enums\FineType;
use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidFineException;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

final class Fine extends AbstractStructuredValueObject
{
	public function __construct(
		public readonly float $value,
		public readonly FineType $type,
	) {}

	public static function create(float $value, string $type): self
	{
		if ($value < 0) {
			throw new InvalidFineException('Fine value cannot be negative');
		}


		$type = FineType::tryFromString($type);

		if ($type === null) {
			throw new InvalidFineException('Invalid fine type');
		}


		// Validate percentage
		if ($type === FineType::Percentage && $value > 100) {
			throw new InvalidFineException('Fine percentage cannot exceed 100%');
		}

		return new self($value, $type);
	}

	public static function fromArray(array $data): self
	{
		return self::create(
			value: $data['value'] ?? throw new InvalidFineException('Fine value is required'),
			type: $data['type'] ?? FineType::Percentage->value
		);
	}
}
