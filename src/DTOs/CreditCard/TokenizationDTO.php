<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\CreditCard;

use AsaasPhpSdk\DTOs\Attributes\SerializeAs;
use AsaasPhpSdk\DTOs\Base\AbstractDTO;
use AsaasPhpSdk\Exceptions\DTOs\CreditCard\InvalidCreditCardDataException;
use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;
use AsaasPhpSdk\Support\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Structured\CreditCard;
use AsaasPhpSdk\ValueObjects\Structured\CreditCardHolderInfo;

/**
 * A "Strict" Data Transfer Object for tokenizing a credit card.
 *
 * This DTO validates the structure, format, and internal consistency of the
 * credit card data upon creation. It ensures that an instance of this class can
 * only exist in a valid state, throwing an `InvalidCreditCardDataException` if any
 * rule is violated.
 */
final readonly class TokenizationDTO extends AbstractDTO
{
    /**
     * Protected constructor for TokenizationDTO.
     *
     * @internal
     *
     * @param  string  $customer  The customer ID.
     * @param  CreditCard  $creditCard  The credit card details.
     * @param  CreditCardHolderInfo  $creditCardHolderInfo  The credit card holder information.
     * @param  string  $remoteIp  The remote IP address.
     */
    /** @phpstan-ignore-next-line Constructor signature intentionally differs from AbstractDTO for factory pattern */
    protected function __construct(
        public string $customer,
        #[SerializeAs(method: 'toArray')]
        public CreditCard $creditCard,
        #[SerializeAs(method: 'toArray')]
        public CreditCardHolderInfo $creditCardHolderInfo,
        public string $remoteIp
    ) {}

    /**
     * @internal
     */
    protected static function sanitize(array $data): array
    {
        return [
            'customer' => DataSanitizer::sanitizeString($data['customer'] ?? null),
            'creditCard' => $data['creditCard'] ?? null,
            'creditCardHolderInfo' => $data['creditCardHolderInfo'] ?? null,
            'remoteIp' => DataSanitizer::sanitizeString($data['remoteIp'] ?? null),
        ];
    }

    /**
     * Validates the provided data for creating a TokenizationDTO.
     *
     * @internal
     *
     * @param  array{
     *   customer?: string,
     *   creditCard?: array<string,mixed>,
     *   creditCardHolderInfo?: array<string,mixed>,
     *   creditCardToken?: string,
     *   remoteIp?: string
     * } $data The data to create the DTO from.  $data  The data to validate.
     * @return array<string, mixed> The validated data.
     *
     * @throws InvalidCreditCardDataException If validation fails.
     */
    protected static function validate(array $data): array
    {
        if (empty($data['customer'])) {
            throw new InvalidCreditCardDataException('Customer ID cannot be empty');
        }

        if (empty($data['creditCard'])) {
            throw new InvalidCreditCardDataException('Credit card cannot be empty');
        }

        if (empty($data['creditCardHolderInfo'])) {
            throw new InvalidCreditCardDataException('Credit card holder info cannot be empty');
        }

        if (empty($data['remoteIp'])) {
            throw new InvalidCreditCardDataException('Remote IP cannot be empty');
        }

        if (! filter_var($data['remoteIp'], FILTER_VALIDATE_IP)) {
            throw new InvalidCreditCardDataException('Remote IP must be a valid IPv4 or IPv6 address');
        }

        try {
            self::validateStructuredValueObject($data, 'creditCard', CreditCard::class);
            self::validateStructuredValueObject($data, 'creditCardHolderInfo', CreditCardHolderInfo::class);
        } catch (InvalidValueObjectException $e) {
            throw new InvalidCreditCardDataException('Invalid credit card data: '.$e->getMessage(), 0, $e);
        }

        return $data;
    }
}
