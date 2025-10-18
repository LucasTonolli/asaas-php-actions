<?php

use AsaasPhpSdk\DTOs\Payments\CreatePaymentDTO;
use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\ValueObjects\Structured\Discount;
use AsaasPhpSdk\ValueObjects\Structured\Interest;

describe('CreatePaymentDTO', function (): void {
    it('creates a payment DTO with valid data', function () {
        $data = [
            'customer' => 'cus_12345',
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 150.75,
            'dueDate' => '2025-12-31',
            'description' => 'Test payment',
        ];

        $dto = CreatePaymentDTO::fromArray($data);

        expect($dto)
            ->toBeInstanceOf(CreatePaymentDTO::class)
            ->customer->toBe('cus_12345')
            ->billingType->toBeInstanceOf(BillingTypeEnum::class)
            ->value->toBe(150.75)
            ->dueDate->toBeInstanceOf(DateTimeImmutable::class)
            ->description->toBe('Test payment');
        expect($dto->toArray())->toMatchArray($data);
    });

    it('throws an exception if customer is missing', function () {
        $data = [
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 100,
            'dueDate' => '2025-12-31',
        ];

        CreatePaymentDTO::fromArray($data);
    })->throws(InvalidPaymentDataException::class, "Required field 'customer' is missing");

    it('throws an exception if value is missing', function () {
        $data = [
            'customer' => 'cus_12345',
            'billingType' => BillingTypeEnum::Boleto->value,
            'dueDate' => '2025-12-31',
        ];

        CreatePaymentDTO::fromArray($data);
    })->throws(InvalidPaymentDataException::class, 'Value must be greater than 0');

    it('throws an exception if billingType is invalid', function () {
        $data = [
            'customer' => 'cus_12345',
            'billingType' => 'INVALID_TYPE',
            'value' => 100,
            'dueDate' => '2025-12-31',
        ];

        CreatePaymentDTO::fromArray($data);
    })->throws(InvalidPaymentDataException::class, 'Invalid billing type');

    it('throws an exception if dueDate is invalid', function () {
        $data = [
            'customer' => 'cus_12345',
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 100,
            'dueDate' => 'invalid-date',
        ];

        CreatePaymentDTO::fromArray($data);
    })->throws(InvalidPaymentDataException::class, 'Invalid due date format');

    it('validates and creates structured value objects', function () {
        $data = [
            'customer' => 'cus_12345',
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 100,
            'dueDate' => '2025-12-31',
            'discount' => ['value' => 10.0, 'dueDateLimitDays' => 5],
            'interest' => ['value' => 1.5],
        ];

        $dto = CreatePaymentDTO::fromArray($data);

        expect($dto->discount)->toBeInstanceOf(Discount::class)
            ->and($dto->interest)->toBeInstanceOf(Interest::class);
    });

    it('throws an exception if discount value object is invalid', function () {
        $data = [
            'customer' => 'cus_12345',
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 100,
            'dueDate' => '2025-12-31',
            'discount' => ['value' => -10], // invalid example
        ];

        CreatePaymentDTO::fromArray($data);
    })->throws(InvalidPaymentDataException::class);
});
