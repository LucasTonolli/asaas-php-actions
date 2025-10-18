<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured\Enums;

use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\Support\Traits\Enums\EnumEnhancements;

/**
 * Defines the possible types for a discount.
 *
 * This enum represents whether a discount is a fixed monetary value
 * or a percentage of the total amount.
 */
enum DiscountType: string
{
    use EnumEnhancements;

    case Fixed = 'FIXED';
    case Percentage = 'PERCENTAGE';

    /**
     * Gets the human-readable label for the discount type.
     *
     * @return string The label in Portuguese (e.g., 'Fixo', 'Porcentagem').
     */
    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Fixo',
            self::Percentage => 'Porcentagem',
        };
    }

    /**
     * Creates an enum instance from various case-insensitive string representations.
     *
     * @internal This is the strict factory, used by `tryFromString`.
     *
     * @param  string  $value  The string representation of the type (e.g., 'fixed', 'fixo').
     * @return self The corresponding enum instance.
     *
     * @throws \ValueError If the string does not match any known type.
     */
    private static function fromString(string $value): self
    {
        $normalized = DataSanitizer::sanitizeLowercase($value);

        return match (true) {
            in_array($normalized, ['fixed', 'fixo']) => self::Fixed,
            in_array($normalized, ['percentage', 'porcentagem']) => self::Percentage,
            default => throw new \ValueError("Invalid discount type '{$value}'"),
        };
    }
}
