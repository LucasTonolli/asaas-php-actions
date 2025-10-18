<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments;

use AsaasPhpSdk\DTOs\Attributes\SerializeAs;
use AsaasPhpSdk\DTOs\Base\AbstractDTO;
use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Structured\Callback;
use AsaasPhpSdk\ValueObjects\Structured\Discount;
use AsaasPhpSdk\ValueObjects\Structured\Fine;
use AsaasPhpSdk\ValueObjects\Structured\Interest;
use AsaasPhpSdk\ValueObjects\Structured\Split;

/**
 * A "Strict" Data Transfer Object for creating a new payment.
 *
 * This DTO validates the structure, format, and internal consistency of the
 * payment data upon creation. It ensures that an instance of this class can only
 * exist in a valid state, throwing an `InvalidPaymentDataException` if any
 * rule is violated.
 */
final class CreatePaymentDTO extends AbstractDTO
{
    /**
     * Private constructor to enforce object creation via the static `fromArray` factory method.
     *
     * @param  string  $customer  The ID of the customer to whom the payment belongs.
     * @param  BillingTypeEnum  $billingType  The payment method.
     * @param  float  $value  The monetary value of the payment.
     * @param  \DateTimeImmutable  $dueDate  The payment's due date.
     * @param  ?string  $description  Optional description for the payment.
     * @param  ?int  $daysAfterDueDateToRegistrationCancellation  Optional number of days after due date to cancel registration.
     * @param  ?string  $externalReference  A unique external identifier.
     * @param  ?int  $installmentCount  Number of installments (for credit card payments).
     * @param  ?float  $totalValue  Total value if it's a parcelled payment. For installments: when provided, installmentValue is not required (calculated from totalValue / installmentCount)
     * @param  ?float  $installmentValue  Value of each installment.
     * @param  ?Discount  $discount  Discount settings.
     * @param  ?Interest  $interest  Interest settings for late payment.
     * @param  ?Fine  $fine  Fine settings for late payment.
     * @param  ?bool  $postalService  Indicates if the invoice should be sent by postal service.
     * @param  ?Split  $split  Payment split settings.
     * @param  ?Callback  $callback  Callback and redirection settings.
     */
    private function __construct(
        public readonly string $customer,
        public readonly BillingTypeEnum $billingType,
        public readonly float $value,
        #[SerializeAs(method: 'format', args: ['Y-m-d'])]
        public readonly \DateTimeImmutable $dueDate,
        public readonly ?string $description = null,
        public readonly ?int $daysAfterDueDateToRegistrationCancellation = null,
        public readonly ?string $externalReference = null,
        public readonly ?int $installmentCount = null,
        public readonly ?float $totalValue = null,
        public readonly ?float $installmentValue = null,
        #[SerializeAs(method: 'toArray')]
        public readonly ?Discount $discount = null,
        #[SerializeAs(method: 'toArray')]
        public readonly ?Interest $interest = null,
        #[SerializeAs(method: 'toArray')]
        public readonly ?Fine $fine = null,
        public readonly ?bool $postalService = null,
        #[SerializeAs(method: 'toArray')]
        public readonly ?Split $split = null,
        #[SerializeAs(method: 'toArray')]
        public readonly ?Callback $callback = null
    ) {}

    /**
     * Creates a new CreatePaymentDTO instance from a raw array of data.
     *
     * @param  array<string, mixed>  $data  Raw data for the new payment.
     * @return self A new, validated instance of the DTO.
     *
     * @throws InvalidPaymentDataException if the data is invalid.
     */
    public static function fromArray(array $data): self
    {
        $sanitizedData = self::sanitize($data);
        $validatedData = self::validate($sanitizedData);

        return new self(...$validatedData);
    }

    /**
     * @internal
     */
    protected static function sanitize(array $data): array
    {
        return [
            'customer' => DataSanitizer::sanitizeString($data['customer'] ?? null),
            'billingType' => $data['billingType'] ?? null,
            'value' => DataSanitizer::sanitizeFloat($data['value'] ?? null),
            'dueDate' => $data['dueDate'] ?? null,
            'description' => self::optionalString($data, 'description'),
            'daysAfterDueDateToRegistrationCancellation' => self::optionalInteger($data, 'daysAfterDueDateToRegistrationCancellation'),
            'externalReference' => self::optionalString($data, 'externalReference'),
            'installmentCount' => self::optionalInteger($data, 'installmentCount'),
            'totalValue' => self::optionalFloat($data, 'totalValue'),
            'installmentValue' => self::optionalFloat($data, 'installmentValue'),
            'discount' => $data['discount'] ?? null,
            'interest' => $data['interest'] ?? null,
            'fine' => $data['fine'] ?? null,
            'postalService' => self::optionalBoolean($data, 'postalService'),
            'split' => $data['split'] ?? null,
            'callback' => $data['callback'] ?? null,
        ];
    }

    /**
     * Validates the sanitized data for the create operation.
     * 
     * @internal
     * 
     * @param  array<string, mixed>  $data  The sanitized data to validate.
     * @return array<string, mixed> The validated data.
     * 
     * @throws InvalidPaymentDataException|InvalidValueObjectException
     */
    private static function validate(array $data): array
    {
        if ($data['customer'] === null) {
            throw InvalidPaymentDataException::missingField('customer');
        }

        if ($data['billingType'] === null) {
            throw InvalidPaymentDataException::missingField('billingType');
        }

        if ($data['value'] === null || $data['value'] <= 0) {
            throw new InvalidPaymentDataException('Value must be greater than 0');
        }

        if ($data['dueDate'] === null) {
            throw InvalidPaymentDataException::missingField('dueDate');
        }

        if ($data['billingType'] instanceof BillingTypeEnum) {
            $billingType = $data['billingType'];
        } else {
            $billingType = BillingTypeEnum::tryFromString((string) $data['billingType']);
        }

        if ($billingType === null) {
            throw new InvalidPaymentDataException('Invalid billing type');
        }
        $data['billingType'] = $billingType;

        try {
            $data['dueDate'] = $data['dueDate'] instanceof \DateTimeImmutable
                ? $data['dueDate']
                : new \DateTimeImmutable((string) $data['dueDate']);
        } catch (\Exception $e) {
            throw new InvalidPaymentDataException('Invalid due date format', 0, $e);
        }

        try {
            self::validateStructuredValueObject($data, 'discount', Discount::class);
            self::validateStructuredValueObject($data, 'interest', Interest::class);
            self::validateStructuredValueObject($data, 'fine', Fine::class);
            self::validateStructuredValueObject($data, 'split', Split::class);
            self::validateStructuredValueObject($data, 'callback', Callback::class);
        } catch (InvalidValueObjectException $e) {
            throw new InvalidPaymentDataException($e->getMessage(), 0, $e);
        }

        if ($data['split'] instanceof Split) {
            try {
                $data['split']->validateFor($data['value']);
            } catch (\Throwable $e) {
                throw new InvalidPaymentDataException($e->getMessage(), 0, $e);
            }
        }

        $hasInstallmentData = $data['installmentCount'] !== null || $data['installmentValue'] !== null || $data['totalValue'] !== null;
        if ($hasInstallmentData && $data['billingType'] !== BillingTypeEnum::CreditCard) {
            throw new InvalidPaymentDataException('Installment fields can only be used with CREDIT_CARD billing type.');
        }

        if ($data['installmentCount'] !== null && $data['installmentCount'] <= 0) {
            throw new InvalidPaymentDataException('Installment count must be greater than 0');
        }

        if ($data['installmentValue'] !== null && $data['installmentValue'] <= 0) {
            throw new InvalidPaymentDataException('Installment value must be greater than 0');
        }

        if ($data['totalValue'] !== null && $data['totalValue'] <= 0) {
            throw new InvalidPaymentDataException('Total value must be greater than 0');
        }

        if ($data['installmentValue'] === null && $data['installmentCount'] !== null && $data['totalValue'] !== null) {
            $data['installmentValue'] = round($data['totalValue'] / $data['installmentCount'], 2);
        }

        return $data;
    }
}
