<?php

use AsaasPhpSdk\DTOs\CreditCard\TokenizationDTO;
use AsaasPhpSdk\Exceptions\DTOs\CreditCard\InvalidCreditCardDataException;

dataset('tokenization_credit_card_missing_fields', [
    [[
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => (string) ((int) date('Y') + 1),
        'ccv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'expiryMonth' => '12',
        'expiryYear' => (string) ((int) date('Y') + 1),
        'ccv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'ccv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'number' => '4111111111111111',
        'expiryYear' => (string) ((int) date('Y') + 1),
        'ccv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => (string) ((int) date('Y') + 1),
    ]],
]);

dataset(
    'tokenization_credit_card_holder_info_missing_fields',
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

describe('Tokenization DTO', function (): void {
    it('creates a tokenization DTO with valid data', function (): void {
        $data = [
            'customer' => 'cus_12345',
            'creditCard' => [
                'holderName' => 'John Doe',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => (string) ((int) date('Y') + 1),
                'ccv' => '123',
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
            'remoteIp' => '127.0.0.1',
        ];

        $tokenization = TokenizationDTO::fromArray($data);

        expect($tokenization->creditCard->holderName)->toEqual('John Doe');
        expect($tokenization->creditCard->number)->toEqual('4111111111111111');
        expect($tokenization->creditCard->expiryMonth)->toEqual('12');
        expect($tokenization->creditCard->expiryYear)->toEqual((string) ((int) date('Y') + 1));
        expect($tokenization->creditCard->ccv)->toEqual('123');
        expect($tokenization->creditCardHolderInfo->name)->toEqual('John Doe');
        expect($tokenization->creditCardHolderInfo->cpfCnpj->formatted())->toEqual('824.121.180-51');
        expect($tokenization->creditCardHolderInfo->email->value())->toEqual('vxh8v@example.com');
        expect($tokenization->creditCardHolderInfo->phone->value())->toEqual('1234567890');
        expect($tokenization->creditCardHolderInfo->mobilePhone->value())->toEqual('1234567890');
        expect($tokenization->creditCardHolderInfo->postalCode->formatted())->toEqual('12345-678');
        expect($tokenization->creditCardHolderInfo->addressNumber)->toEqual('123');
    });

    it('throws an exception if required credit card fields are missing', function (): void {
        expect(fn () => TokenizationDTO::fromArray([
            'customer' => 'cus_12345',
            'creditCardHolderInfo' => [
                'name' => 'John Doe',
                'cpfCnpj' => '824.121.180-51',
                'email' => 'VxH8V@example.com',
                'phone' => '1234567890',
                'mobilePhone' => '1234567890',
                'postalCode' => '12345-678',
                'addressNumber' => '123',
            ],
            'remoteIp' => '127.0.0.1',
        ]))->toThrow(InvalidCreditCardDataException::class);
    })->with('tokenization_credit_card_missing_fields');

    it('throws an exception if required credit card holder info fields are missing', function (): void {
        expect(fn () => TokenizationDTO::fromArray([
            'customer' => 'cus_12345',
            'creditCard' => [
                'holderName' => 'John Doe',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => (string) ((int) date('Y') + 1),
                'ccv' => '123',
            ],
            'remoteIp' => '127.0.0.1',
        ]))->toThrow(InvalidCreditCardDataException::class);
    })->with('tokenization_credit_card_holder_info_missing_fields');

    it('throws an exception if customer is missing', function (): void {
        expect(fn () => TokenizationDTO::fromArray([
            'creditCard' => [
                'holderName' => 'John Doe',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => (string) ((int) date('Y') + 1),
                'ccv' => '123',
            ],
            'remoteIp' => '127.0.0.1',
        ]))->toThrow(InvalidCreditCardDataException::class);
    });

    it('throws an exception if remoteIp is missing', function (): void {
        expect(fn () => TokenizationDTO::fromArray([
            'customer' => 'cus_12345',
            'creditCard' => [
                'holderName' => 'John Doe',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => (string) ((int) date('Y') + 1),
                'ccv' => '123',
            ],
        ]))->toThrow(InvalidCreditCardDataException::class);
    });
});
