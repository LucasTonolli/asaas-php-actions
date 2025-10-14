<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured\Enums;

use AsaasPhpSdk\Helpers\DataSanitizer;

/**
 * Defines the possible types for a discount.
 *
 * This enum represents whether a discount is a fixed monetary value
 * or a percentage of the total amount.
 */
enum DiscountType: string
{
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

    /**
     * Safely creates an enum instance from various string representations.
     *
     * This is a lenient factory that accepts multiple aliases. If the string
     * is not valid, it returns `null` instead of throwing an exception.
     *
     * @param  string  $value  The string representation of the type.
     * @return self|null The corresponding enum instance or `null` if the value is invalid.
     */
    public static function tryFromString(string $value): ?self
    {
        try {
            return self::fromString($value);
        } catch (\ValueError) {
            return null;
        }
    }

    /**
     * Gets an array containing all possible enum cases.
     *
     * @return array<int, self> An array of all enum instances.
     */
    public static function all(): array
    {
        return [
            self::Fixed,
            self::Percentage,
        ];
    }

    /**
     * Gets a key-value array of all options, suitable for UI elements like dropdowns.
     *
     * The array key is the case name (e.g., 'Fixed') and the value is the
     * human-readable label (e.g., 'Fixo').
     *
     * @return array<string, string> An associative array of options.
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::all() as $discountType) {
            $options[$discountType->name] = $discountType->label();
        }

        return $options;
    }
}
