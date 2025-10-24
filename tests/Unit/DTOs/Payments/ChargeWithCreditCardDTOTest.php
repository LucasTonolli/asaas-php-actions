<?php

use AsaasPhpSdk\DTOs\Payments\ChargeWithCreditCardDTO;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\ValueObjects\Structured\CreditCard;
use AsaasPhpSdk\ValueObjects\Structured\CreditCardHolderInfo;

dataset('credit_card_missing_fields', [
    [[
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => '2025',
        'cvv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'expiryMonth' => '12',
        'expiryYear' => '2025',
        'cvv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'cvv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'number' => '4111111111111111',
        'expiryYear' => '2025',
        'cvv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => '2025',
    ]],
]);

dataset(
    'credit_card_holder_info_missing_fields',
    [
        [[
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => '00000-000',
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]],
        [[
            'name' => 'John Doe',
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => '00000-000',
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]],
        [[
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'postalCode' => '00000-000',
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]],
        [[
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]],
        [[
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => '00000-000',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]],
        [[
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'addressNumber' => '12345',
            'mobilePhone' => ' 11 98888-8888',
        ]],
    ]
);
describe('Charge With Credit Card DTO', function (): void {
    it('creates a payment with credit card DTO with valid data', function (): void {
        $data = [
            'creditCard' => [
                'holderName' => 'John Doe',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'cvv' => '123',
            ],
            'creditCardHolderInfo' => [
                'name' => 'John Doe',
                'cpfCnpj' => '824.121.180-51',
                'email' => 'VxH8V@example.com',
                'phone' => '1234567890',
                'mobilePhone' => '1234567890',
                'postalCode' => '12345-678',
                'addressNumber' => '123',
            ],
        ];

        $dto = ChargeWithCreditCardDTO::fromArray($data);

        expect($dto)
            ->toBeInstanceOf(ChargeWithCreditCardDTO::class)
            ->creditCard->toBeInstanceOf(CreditCard::class)
            ->creditCardHolderInfo->toBeInstanceOf(CreditCardHolderInfo::class);
    });

    it('throws an exception if required credit card fields are missing', function ($creditCardData): void {
        expect(fn() => ChargeWithCreditCardDTO::fromArray([
            'creditCard' => $creditCardData,
            'creditCardHolderInfo' => [
                'name' => 'John Doe',
                'cpfCnpj' => '12345678900',
                'email' => 'VxH8V@example.com',
                'phone' => '1234567890',
            ],
        ]))->toThrow(InvalidPaymentDataException::class);
    })->with('credit_card_missing_fields');

    it('throws an exception if required credit card holder info fields are missing', function ($creditCardHolderInfoData): void {
        expect(fn() => ChargeWithCreditCardDTO::fromArray([
            'creditCard' => [
                'holderName' => 'John Doe',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'cvv' => '123',
            ],
            'creditCardHolderInfo' => $creditCardHolderInfoData,
        ]))->toThrow(InvalidPaymentDataException::class);
    })->with('credit_card_holder_info_missing_fields');

    it('if creditCardToken is provided, creditCard details are not required', function (): void {})->skip('Need to implement this test case [Credit card Tokenization]');
});
