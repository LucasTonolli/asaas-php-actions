<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidDiscountException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;
use AsaasPhpSdk\ValueObjects\Structured\Enums\DiscountType;

/**
 * A Value Object representing a discount to be applied to a payment.
 *
 * This class encapsulates the discount's value, its type (fixed or percentage),
 * and its application rules (e.g., `dueDateLimitDays`). It contains validation
 * to ensure a discount is always in a valid state upon creation.
 */
final readonly class Discount extends AbstractStructuredValueObject
{
    /**
     * Discount private constructor.
     *
     * @internal
     *
     * @param  float  $value  The numeric value of the discount (either fixed amount or percentage).
     * @param  ?int  $dueDateLimitDays  Number of days before due date to apply the discount.
     * @param  DiscountType  $discountType  The type of discount (Fixed or Percentage).
     */
    private function __construct(
        public float $value,
        public ?int $dueDateLimitDays,
        public DiscountType $discountType
    ) {}

    /**
     * Creates a new Discount instance with explicit, validated parameters.
     *
     * This is the primary factory, performing all core business logic validations.
     *
     * @param  float  $value  The amount or percentage of the discount.
     * @param  ?int  $dueDateLimitDays  The number of days before the due date that the discount is valid.
     * @param  string  $discountType  The type of discount (e.g., 'fixed', 'percentage').
     * @return self A new, validated Discount instance.
     *
     * @throws InvalidDiscountException If the value is not positive, the type is invalid, or a percentage exceeds 100.
     */
    public static function create(float $value, ?int $dueDateLimitDays, string $discountType): self
    {
        $sanitizedDueDateLimitDays = DataSanitizer::sanitizeInteger($dueDateLimitDays);
        $sanitizedValue = DataSanitizer::sanitizeFloat($value);
        $discountType = DataSanitizer::sanitizeLowercase($discountType);
        $type = DiscountType::tryFromString($discountType);

        if (! is_finite($sanitizedValue)) {
            throw new InvalidDiscountException('Discount value must be a finite number');
        }

        if ($sanitizedValue === null || $sanitizedValue <= 0) {
            throw new InvalidDiscountException('Value must be greater than 0.');
        }

        if ($type === null) {
            throw new InvalidDiscountException('Invalid discount type');
        }

        if ($type === DiscountType::Percentage && $sanitizedValue > 100) {
            throw new InvalidDiscountException('Discount percentage cannot exceed 100%');
        }

        return new self($sanitizedValue, $sanitizedDueDateLimitDays, $type);
    }

    /**
     * Creates a Discount instance from a raw data array.
     *
     * This factory handles array-based input, checks for required keys, and
     * delegates to the `create()` method for core validation.
     *
     * @param  array{value?: float|string|null, dueDateLimitDays?: int|string|null, type?: string}  $data  The raw data array.
     * @return self A new, validated Discount instance.
     *
     * @throws InvalidDiscountException If required keys are missing from the array.
     */
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

    /**
     * Calculates the concrete discount amount for a given payment value.
     *
     * If the discount is 'Fixed', it returns the fixed value. If it's 'Percentage',
     * it calculates the percentage of the provided `paymentValue`.
     *
     * @param  float  $paymentValue  The total value of the payment on which to apply a percentage discount.
     * @return float The calculated discount amount.
     */
    public function calculateAmount(float $paymentValue): float
    {
        return match ($this->discountType) {
            DiscountType::Fixed => $this->value,
            DiscountType::Percentage => ($paymentValue * $this->value) / 100,
        };
    }
}
