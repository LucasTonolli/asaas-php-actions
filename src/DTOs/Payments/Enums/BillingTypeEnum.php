<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments\Enums;

use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\Support\Traits\Enums\EnumEnhancements;

/**
 * Defines the possible billing types for a payment.
 */
enum BillingTypeEnum: string
{
    use EnumEnhancements;

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
     *
     * @param  string  $value  The string representation of the type (e.g., 'boleto', 'credit_card').
     * @return self The corresponding enum instance.
     *
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
}
