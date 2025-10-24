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
use Symfony\Component\VarDumper\Cloner\Data;

final readonly class ChargeWithCreditCardDTO extends AbstractDTO
{
    /**
     * Private constructor to enforce object creation via the static `fromArray` factory method.
     *
     * @param  CreatePaymentDTO  $payment  The payment details.
     * @param  CreditCard  $creditCard  The credit card details.
     */
    private function __construct(
        #[SerializeAs(method: 'toArray')]
        public ?CreditCard $creditCard,
        #[SerializeAs(method: 'toArray')]
        public ?CreditCardHolderInfo $creditCardHolderInfo,
        public ?string $creditCardToken,
    ) {}

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
        $data['creditCardToken'] = DataSanitizer::sanitizeString($data['creditCardToken'] ?? null);

        return $data;
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
        if (is_null($data['creditCardToken'])) {
            if (is_null($data['creditCard'])) {
                throw new InvalidPaymentDataException('Credit card details are required when credit card token is not provided.');
            }

            if (is_null($data['creditCardHolderInfo'])) {
                throw new InvalidPaymentDataException('Credit card holder info is required when credit card token is not provided.');
            }
        }

        try {

            $data['creditCard'] = CreditCard::fromArray($data['creditCard']);

            $data['creditCardHolderInfo'] = CreditCardHolderInfo::fromArray($data['creditCardHolderInfo']);
        } catch (InvalidValueObjectException $e) {
            throw new InvalidPaymentDataException($e->getMessage(), 0, $e);
        }

        return $data;
    }
}
