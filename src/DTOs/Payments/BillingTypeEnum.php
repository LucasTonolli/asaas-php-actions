<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments;

use AsaasPhpSdk\Helpers\DataSanitizer;

enum BillingTypeEnum: string
{
    case Undefined = 'UNDEFINED';
    case Boleto = 'BOLETO';
    case CreditCard = 'CREDIT_CARD';
    case Pix = 'PIX';

    public function label(): string
    {
        return match ($this) {
            self::Boleto => 'Boleto',
            self::CreditCard => 'Cartão de Crédito',
            self::Pix => 'Pix',
            self::Undefined => 'Indefinido',
        };
    }

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

    public static function tryFromString(string $value): ?self
    {
        try {
            return self::fromString($value);
        } catch (\ValueError) {
            return null;
        }
    }

    public static function all(): array
    {
        return [
            self::Boleto,
            self::CreditCard,
            self::Pix,
            self::Undefined,
        ];
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::all() as $billingType) {
            $options[$billingType->name] = $billingType->label();
        }

        return $options;
    }
}
