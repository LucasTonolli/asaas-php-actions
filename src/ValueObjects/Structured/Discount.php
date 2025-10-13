<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Enums\DiscountType;
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
		if ($value <= 0) {
			throw new InvalidDiscountException('Value must be greater than 0.');
		}

		$saninitizeddueDateLimitDays = DataSanitizer::sanitizeInteger($dueDateLimitDays);
		$value = DataSanitizer::sanitizeFloat($value);
		$discountType = DataSanitizer::sanitizeLowercase($discountType);
		$type = DiscountType::tryFromString($discountType);

		if ($type === null) {
			throw new InvalidDiscountException('Invalid discount type');
		}

		if ($type === DiscountType::Percentage && $value > 100) {
			throw new InvalidDiscountException('Discount percentage cannot exceed 100%');
		}

		return new self($value, $saninitizeddueDateLimitDays, $type);
	}

	public static function fromArray(array $data): self
	{
		return self::create(
			value: $data['value'] ?? throw new InvalidDiscountException('Discount value is required'),
			dueDateLimitDays: $data['dueDateLimitDays'] ?? throw new InvalidDiscountException('Discount dueDateLimitDays is required'),
			discountType: $data['type'] ?? 'fixed'
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
