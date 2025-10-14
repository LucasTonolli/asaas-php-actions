<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments;

use AsaasPhpSdk\Helpers\DataSanitizer;

/**
 * Defines the possible billing types for a payment.
 */
enum BillingTypeEnum: string
{
    case Undefined = 'UNDEFINED';
    case Boleto = 'BOLETO';
    case CreditCard = 'CREDIT_CARD';
    case Pix = 'PIX';

    /**
     * Gets the human-readable label for the billing type.
     *
     * @return string The label in Portuguese (e.g., 'Boleto', 'Cartão de Crédito').
     */
    public function label(): string
    {
        return match ($this) {
            self::Boleto => 'Boleto',
            self::CreditCard => 'Cartão de Crédito',
            self::Pix => 'Pix',
            self::Undefined => 'Indefinido',
        };
    }

    /**
     * Creates an enum instance from various case-insensitive string representations.
     *
     * @internal This is the strict factory, used by `tryFromString`.
     * @param  string  $value The string representation of the type (e.g., 'boleto', 'credit_card').
     * @return self The corresponding enum instance.
     * @throws \ValueError If the string does not match any known type.
     */
    private static function fromString(string $value): self
    {
        $normalized = DataSanitizer::sanitizeLowercase($value);

        return match (true) {
            in_array($normalized, ['boleto', 'boleto bancario', 'ticket']) => self::Boleto,
            in_array($normalized, ['cartão de crédito', 'credit_card', 'creditcard']) => self::CreditCard,
            $normalized === 'pix' => self::Pix,
            in_array($normalized, ['indefinido', 'undefined']) => self::Undefined,
            default => throw new \ValueError("Invalid billing type '{$value}'"),
        };
    }

    /**
     * Safely creates an enum instance from various string representations.
     *
     * This is a lenient factory that accepts multiple aliases. If the string
     * is not valid, it returns `null` instead of throwing an exception.
     *
     * @param  string  $value The string representation of the type.
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
            self::Boleto,
            self::CreditCard,
            self::Pix,
            self::Undefined,
        ];
    }

    /**
     * Gets a key-value array of all options, suitable for UI elements like dropdowns.
     *
     * The array key is the case name (e.g., 'Boleto') and the value is the
     * human-readable label (e.g., 'Boleto').
     *
     * @return array<string, string> An associative array of options.
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::all() as $billingType) {
            $options[$billingType->name] = $billingType->label();
        }

        return $options;
    }
}
