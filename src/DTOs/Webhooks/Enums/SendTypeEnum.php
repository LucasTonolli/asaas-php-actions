<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Webhooks\Enums;

use AsaasPhpSdk\Support\Helpers\DataSanitizer;
use AsaasPhpSdk\Support\Traits\Enums\EnumEnhancements;

/**
 * Defines the possible send types for webhooks.
 */
enum SendTypeEnum: string
{
    use EnumEnhancements;

    case Sequentially = 'SEQUENTIALLY';
    case NonSequentially = 'NON_SEQUENTIALLY';

    /**
     * Gets the human-readable label for the send type.
     *
     * @return string The label in Portuguese (e.g., 'Sequencial', 'Não Sequencial').
     */
    public function label(): string
    {
        return match ($this) {
            self::Sequentially => 'Sequencial',
            self::NonSequentially => 'Não Sequencial',
        };
    }

    /**
     * Creates an enum instance from a string representation.
     *
     * @internal This is the strict factory, used by `tryFromString`.
     *
     * @param  string  $value  The string representation of the type (e.g., 'sequentially', 'non_sequentially').
     * @return self The corresponding enum instance.
     *
     * @throws \ValueError If the string does not match any known type.
     */
    private static function fromString(string $value): self
    {
        $normalized = DataSanitizer::sanitizeLowercase($value);

        return match (true) {
            $normalized === 'sequentially' => self::Sequentially,
            $normalized === 'non_sequentially' => self::NonSequentially,
            default => throw new \ValueError("Invalid send type '{$value}'"),
        };
    }
}
