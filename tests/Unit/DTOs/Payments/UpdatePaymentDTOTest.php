<?php

use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\DTOs\Payments\UpdatePaymentDTO;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\ValueObjects\Structured\Callback;
use AsaasPhpSdk\ValueObjects\Structured\Discount;
use AsaasPhpSdk\ValueObjects\Structured\Fine;
use AsaasPhpSdk\ValueObjects\Structured\Interest;
use AsaasPhpSdk\ValueObjects\Structured\Split;

dataset('update_payment_missing_fields', [

    [[
        'value' => 150.75,
        'dueDate' => '2025-12-31',
        'message' => 'Required field \'billingType\' is missing.',
    ]],
    [[
        'billingType' => 'credit_card',
        'dueDate' => '2025-12-31',
        'message' => 'Required field \'value\' is missing.',

    ]],
    [[
        'billingType' => 'credit_card',
        'value' => 150.75,
        'message' => 'Required field \'dueDate\' is missing',
    ]],

]);

dataset('update_payment_null_fields', [
    [
        [
            'billingType' => null,
            'value' => 150.75,
            'dueDate' => '2025-12-31',
            'message' => 'Required field \'billingType\' is missing.',
        ],
    ],
    [[
        'billingType' => 'credit_card',
        'value' => null,
        'dueDate' => '2025-12-31',
        'message' => 'Required field \'value\' is missing.',
    ]],
    [[
        'billingType' => 'credit_card',
        'value' => 150.75,
        'dueDate' => null,
        'message' => 'Required field \'dueDate\' is missing',
    ]],

]);

dataset('update_payment_valid_value_objects', [
    [[
        'billingType' => BillingTypeEnum::Boleto->value,
        'value' => 150.75,
        'dueDate' => '2025-12-31',
        'discount' => [
            'value' => 10,
            'type' => 'percentage',
            'dueDateLimitDays' => 30,
        ],
        'class' => Discount::class,
        'classIndex' => 'discount',
    ]],
    [[
        'billingType' => BillingTypeEnum::Boleto->value,
        'value' => 150.75,
        'dueDate' => '2025-12-31',
        'interest' => [
            'value' => 10,
            'type' => 'percentage',
        ],
        'class' => Interest::class,
        'classIndex' => 'interest',
    ]],
    [[
        'billingType' => BillingTypeEnum::Boleto->value,
        'value' => 150.75,
        'dueDate' => '2025-12-31',
        'fine' => [
            'value' => 10,
            'type' => 'percentage',
        ],
        'class' => Fine::class,
        'classIndex' => 'fine',
    ]],
    [[
        'billingType' => BillingTypeEnum::Boleto->value,
        'value' => 150.75,
        'dueDate' => '2025-12-31',
        'split' => [
            [
                'walletId' => 'wallet_id',
                'fixedValue' => 50,
                'dueDate' => '2025-12-31',
            ],
            [
                'walletId' => 'wallet_id',
                'fixedValue' => 50,
                'dueDate' => '2025-12-31',
            ],
        ],
        'class' => Split::class,
        'classIndex' => 'split',
    ]],
    [[
        'billingType' => BillingTypeEnum::Boleto->value,
        'value' => 150.75,
        'dueDate' => '2025-12-31',
        'callback' => [
            'successUrl' => 'https://example.com/callback',
        ],
        'class' => Callback::class,
        'classIndex' => 'callback',
    ]],
]);

describe('Update Payment DTO', function (): void {
    it('creates a update payment DTO with valid data', function (): void {
        $data = [
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 150.75,
            'dueDate' => '2025-12-31',
            'description' => 'Test payment',
        ];

        $dto = UpdatePaymentDTO::fromArray($data);

        expect($dto)
            ->toBeInstanceOf(UpdatePaymentDTO::class)
            ->billingType->toBeInstanceOf(BillingTypeEnum::class)
            ->value->toBe(150.75)
            ->dueDate->toBeInstanceOf(DateTimeImmutable::class)
            ->description->toBe('Test payment');
        expect($dto->toArray())->toMatchArray($data);
    });

    it('throws an exception if required fields are missing', function ($data): void {
        $exceptionMessage = $data['message'];
        unset($data['message']);
        expect(fn() => UpdatePaymentDTO::fromArray($data))->toThrow(InvalidPaymentDataException::class, $exceptionMessage);
    })->with('update_payment_missing_fields');

    it('throws an exception if required fields are null', function ($data): void {
        $exceptionMessage = $data['message'];
        unset($data['message']);
        expect(fn() => UpdatePaymentDTO::fromArray($data))->toThrow(InvalidPaymentDataException::class, $exceptionMessage);
    })->with('update_payment_null_fields');

    it('create update payment DTO with valid value objects', function ($data): void {
        $class = $data['class'];
        $classIndex = $data['classIndex'];
        unset($data['class'], $data['classIndex']);
        $dto = UpdatePaymentDTO::fromArray($data);
        expect($dto->{$classIndex})->toBeInstanceOf($class);
    })->with('update_payment_valid_value_objects');
});
