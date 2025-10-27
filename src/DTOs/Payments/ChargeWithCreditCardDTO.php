<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments;

use AsaasPhpSdk\DTOs\Attributes\SerializeAs;
use AsaasPhpSdk\DTOs\Base\AbstractDTO;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Structured\CreditCard;
use AsaasPhpSdk\ValueObjects\Structured\CreditCardHolderInfo;

/**
 * A "Strict" Data Transfer Object for creating a new payment with a credit card.
 *
 * This DTO validates the structure, format, and internal consistency of the
 * payment data upon creation. It ensures that an instance of this class can only
 * exist in a valid state, throwing an `InvalidPaymentDataException` if any
 * rule is violated.
 */
final readonly class ChargeWithCreditCardDTO extends AbstractDTO
{
    /**
     * Private constructor to enforce object creation via the static `fromArray` factory method.
     *
     * @param  ?CreditCard  $creditCard  The credit card details (required if no token).
     * @param  ?CreditCardHolderInfo  $creditCardHolderInfo  The credit card holder information (required if no token).
     * @param  ?string  $creditCardToken  The tokenized credit card reference (alternative to providing card details).
     */
    private function __construct(
        #[SerializeAs(method: 'toArray')]
        public ?CreditCard $creditCard,
        #[SerializeAs(method: 'toArray')]
        public ?CreditCardHolderInfo $creditCardHolderInfo,
        public ?string $creditCardToken,
    ) {}

    /**
     * Creates a ChargeWithCreditCardDTO from an array of data.
     *
     * @param  array<string, mixed>  $data  The data to create the ChargeWithCreditCardDTO from.
     * @return ChargeWithCreditCardDTO The created ChargeWithCreditCardDTO.
     */
    public static function fromArray(array $data): self
    {
        $sanitizedData = self::sanitize($data);
        $validatedData = self::validate($sanitizedData);

        return new self(
            ...$validatedData
        );
    }

    /**
     * @internal
     */
    protected static function sanitize(array $data): array
    {
        return [
            'creditCard' => $data['creditCard'] ?? null,
            'creditCardHolderInfo' => $data['creditCardHolderInfo'] ?? null,
            'creditCardToken' => DataSanitizer::sanitizeString($data['creditCardToken'] ?? null),
        ];
    }

    /**
     * Validates the provided data for creating a ChargeWithCreditCardDTO.
     *
     * @internal
     *
     * @param  array<string, mixed>  $data  The data to validate.
     * @return array<string, mixed> The validated data.
     *
     * @throws InvalidPaymentDataException If validation fails.
     */
    private static function validate(array $data): array
    {
        $hasToken = ! empty($data['creditCardToken']);
        $hasCreditCard = isset($data['creditCard']) && is_array($data['creditCard']);
        $hasHolderInfo = isset($data['creditCardHolderInfo']) && is_array($data['creditCardHolderInfo']);

        if (! $hasToken && ! $hasCreditCard) {
            throw new InvalidPaymentDataException(
                'Either creditCardToken or creditCard details must be provided.'
            );
        }

        if (! $hasToken && ! $hasHolderInfo) {
            throw new InvalidPaymentDataException(
                'Credit card holder info is required when credit card token is not provided.'
            );
        }

        try {
            self::validateStructuredValueObject($data, 'creditCard', CreditCard::class);
            self::validateStructuredValueObject($data, 'creditCardHolderInfo', CreditCardHolderInfo::class);
        } catch (InvalidValueObjectException $e) {
            throw new InvalidPaymentDataException(
                'Invalid credit card data: '.$e->getMessage(),
                0,
                $e
            );
        }

        return $data;
    }
}
