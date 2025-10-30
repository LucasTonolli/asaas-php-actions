<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidFineException;
use AsaasPhpSdk\Support\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;
use AsaasPhpSdk\ValueObjects\Structured\Enums\FineType;

/**
 * A Value Object representing a fine (multa) to be applied to a late payment.
 *
 * This class encapsulates the fine's value and its type (fixed or percentage).
 * It contains validation to ensure a fine is always in a valid state upon creation.
 */
final readonly class Fine extends AbstractStructuredValueObject
{
    /**
     * Fine private constructor.
     *
     * @internal Forces creation via static factory methods.
     *
     * @param  float  $value  The numeric value of the fine.
     * @param  FineType  $type  The type of fine (Fixed or Percentage).
     */
    private function __construct(
        public float $value,
        public FineType $type,
    ) {}

    /**
     * Creates a new Fine instance with explicit, validated parameters.
     *
     * This is the primary factory, performing all core business logic validations.
     *
     * @param  float  $value  The amount or percentage of the fine.
     * @param  string  $type  The type of fine (e.g., 'fixed', 'percentage').
     * @return self A new, validated Fine instance.
     *
     * @throws InvalidFineException If the value is negative, the type is invalid, or a percentage exceeds 100.
     */
    private static function create(float $value, string $type): self
    {
        if (! is_finite($value)) {
            throw new InvalidFineException('Fine value must be a finite number');
        }
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

    /**
     * Creates a Fine instance from a raw data array.
     *
     * @param  array{value?: float|string, type?: string}  $data  The raw data array.
     * @return self A new, validated Fine instance.
     *
     * @throws InvalidFineException If the required 'value' key is missing from the array.
     */
    public static function fromArray(array $data): self
    {
        $value = DataSanitizer::sanitizeFloat($data['value'] ?? null);

        if ($value === null) {
            throw new InvalidFineException('Fine value is required');
        }

        $type = (string) ($data['type'] ?? FineType::Percentage->value);

        return self::create(
            value: $value,
            type: $type
        );
    }
}
