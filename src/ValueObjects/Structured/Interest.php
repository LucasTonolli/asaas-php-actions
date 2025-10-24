<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidInterestException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

/**
 * A Value Object representing an interest (juros) percentage for late payments.
 *
 * This class encapsulates the interest rate and ensures it is always a valid
 * percentage (between 0 and 100) upon creation.
 */
final readonly class Interest extends AbstractStructuredValueObject
{
    /**
     * Interest private constructor.
     *
     * @internal Forces creation via static factory methods.
     *
     * @param  float  $value  The interest percentage value.
     */
    private function __construct(
        public float $value,
    ) {}

    /**
     * Creates a new Interest instance with an explicit, validated percentage value.
     *
     * This is the primary factory, performing all core validations to ensure the
     * value is between 0 and 100.
     *
     * @param  float  $value  The interest percentage.
     * @return self A new, validated Interest instance.
     *
     * @throws InvalidInterestException If the value is negative or exceeds 100.
     */
    public static function create(float $value): self
    {
        if (! is_finite($value)) {
            throw new InvalidInterestException('Interest value must be a finite number');
        }
        if ($value < 0) {
            throw new InvalidInterestException('Interest value cannot be negative');
        }

        if ($value > 100) {
            throw new InvalidInterestException('Interest value cannot exceed 100%');
        }

        return new self($value);
    }

    /**
     * Creates an Interest instance from a raw data array.
     *
     * @param  array{value?: float}  $data  The raw data array.
     * @return self A new, validated Interest instance.
     *
     * @throws InvalidInterestException If the required 'value' key is missing.
     */
    public static function fromArray(array $data): self
    {
        $value = DataSanitizer::sanitizeFloat($data['value'] ?? null);

        if ($value === null) {
            throw new InvalidInterestException('Interest value is required');
        }

        return self::create(value: $value);
    }
}
