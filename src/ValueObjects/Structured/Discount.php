<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\ValueObjects\Structured\Enums\DiscountType;
use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidDiscountException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

final class Discount extends AbstractStructuredValueObject
{
	private function __construct(
		public readonly float $value,
		public readonly ?int $dueDateLimitDays,
		public readonly DiscountType $discountType
	) {}

	public static function create(float $value, ?int $dueDateLimitDays, string $discountType): self
	{
		$saninitizedDueDateLimitDays = DataSanitizer::sanitizeInteger($dueDateLimitDays);
		$sanitizedValue = DataSanitizer::sanitizeFloat($value);
		$discountType = DataSanitizer::sanitizeLowercase($discountType);
		$type = DiscountType::tryFromString($discountType);

		if ($sanitizedValue === null || $sanitizedValue <= 0) {
			throw new InvalidDiscountException('Value must be greater than 0.');
		}

		if ($type === null) {
			throw new InvalidDiscountException('Invalid discount type');
		}

		if ($type === DiscountType::Percentage && $sanitizedValue > 100) {
			throw new InvalidDiscountException('Discount percentage cannot exceed 100%');
		}

		return new self($sanitizedValue, $saninitizedDueDateLimitDays, $type);
	}

	public static function fromArray(array $data): self
	{

		$value = DataSanitizer::sanitizeFloat($data['value'] ?? null);
		$days = DataSanitizer::sanitizeInteger($data['dueDateLimitDays'] ?? null);

		if ($value === null) {
			throw new InvalidDiscountException('Discount value is required');
		}

		if ($days === null) {
			throw new InvalidDiscountException('Discount dueDateLimitDays is required');
		}

		return self::create(
			value: $value,
			dueDateLimitDays: $days,
			discountType: (string) ($data['type'] ?? 'fixed')
		);
	}

	public function calculateAmount(float $paymentValue): float
	{
		return match ($this->discountType) {
			DiscountType::Fixed => $this->value,
			DiscountType::Percentage => ($paymentValue * $this->value) / 100,
		};
	}
}
