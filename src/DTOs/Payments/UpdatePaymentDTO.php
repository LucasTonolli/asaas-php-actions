<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments;

use AsaasPhpSdk\DTOs\Attributes\SerializeAs;
use AsaasPhpSdk\DTOs\Base\AbstractDTO;
use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;
use AsaasPhpSdk\Support\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Structured\Callback;
use AsaasPhpSdk\ValueObjects\Structured\Discount;
use AsaasPhpSdk\ValueObjects\Structured\Fine;
use AsaasPhpSdk\ValueObjects\Structured\Interest;
use AsaasPhpSdk\ValueObjects\Structured\Split;
use DateTimeImmutable;

/**
 * A "Strict" Data Transfer Object for updating an existing payment.
 *
 * This DTO validates the structure, format, and internal consistency of the
 * payment data upon creation. It ensures that an instance of this class can only
 * exist in a valid state, throwing an `InvalidPaymentDataException` if any
 * rule is violated.
 */
final readonly class UpdatePaymentDTO extends AbstractDTO
{
    /**
     * Private constructor to enforce object creation via the static `fromArray` factory method.
     *
     * @param  BillingTypeEnum  $billingType  The payment method.
     * @param  float  $value  The monetary value of the payment.
     * @param  \DateTimeImmutable  $dueDate  The payment's due date.
     * @param  ?string  $description  Optional description for the payment.
     * @param  ?int  $daysAfterDueDateToRegistrationCancellation  Optional number of days after due date to cancel registration.
     * @param  ?string  $externalReference  A unique external identifier.
     * @param  ?Discount  $discount  Discount settings.
     * @param  ?Interest  $interest  Interest settings for late payment.
     * @param  ?Fine  $fine  Fine settings for late payment.
     * @param  ?bool  $postalService  Indicates if the invoice should be sent by postal service.
     * @param  ?Split  $split  Payment split settings.
     * @param  ?Callback  $callback  Callback and redirection settings.
     */
    private function __construct(
        public BillingTypeEnum $billingType,
        public float $value,
        #[SerializeAs(method: 'format', args: ['Y-m-d'])]
        public DateTimeImmutable $dueDate,
        public ?string $description = null,
        public ?int $daysAfterDueDateToRegistrationCancellation = null,
        public ?string $externalReference = null,
        #[SerializeAs(method: 'toArray')]
        public ?Discount $discount = null,
        #[SerializeAs(method: 'toArray')]
        public ?Interest $interest = null,
        #[SerializeAs(method: 'toArray')]
        public ?Fine $fine = null,
        public ?bool $postalService = null,
        #[SerializeAs(method: 'toArray')]
        public ?Split $split = null,
        #[SerializeAs(method: 'toArray')]
        public ?Callback $callback = null
    ) {}

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
            'billingType' => $data['billingType'] ?? null,
            'value' => DataSanitizer::sanitizeFloat($data['value'] ?? null),
            'dueDate' => $data['dueDate'] ?? null,
            'description' => self::optionalString($data, 'description'),
            'daysAfterDueDateToRegistrationCancellation' => self::optionalInteger($data, 'daysAfterDueDateToRegistrationCancellation'),
            'externalReference' => self::optionalString($data, 'externalReference'),
            'discount' => $data['discount'] ?? null,
            'interest' => $data['interest'] ?? null,
            'fine' => $data['fine'] ?? null,
            'postalService' => self::optionalBoolean($data, 'postalService'),
            'split' => $data['split'] ?? null,
            'callback' => $data['callback'] ?? null,
        ];
    }

    /**
     * Validates the sanitized data to ensure it follows the expected structure.
     *
     * @internal
     *
     * @param  array<string, mixed>  $data  The sanitized data to validate.
     * @return array<string, mixed> The validated data.
     *
     * @throws InvalidPaymentDataException
     */
    private static function validate(array $data): array
    {
        if (empty($data['billingType'])) {
            throw InvalidPaymentDataException::missingField('billingType');
        }

        if (empty($data['value'])) {
            throw InvalidPaymentDataException::missingField('value');
        }

        if (empty($data['dueDate'])) {
            throw InvalidPaymentDataException::missingField('dueDate');
        }

        $data['billingType'] = BillingTypeEnum::tryFromString($data['billingType']);

        if (empty($data['billingType'])) {
            throw new InvalidPaymentDataException('Invalid billing type.');
        }

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
            throw new InvalidPaymentDataException($e->getMessage(), 400, $e);
        }

        if ($data['split'] instanceof Split) {
            try {
                $data['split']->validateFor($data['value']);
            } catch (\Throwable $e) {
                throw new InvalidPaymentDataException($e->getMessage(), 400, $e);
            }
        }

        return $data;
    }
}
