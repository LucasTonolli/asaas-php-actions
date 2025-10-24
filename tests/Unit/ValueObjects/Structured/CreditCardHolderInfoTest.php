<?php

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidCreditCardHolderInfoException;
use AsaasPhpSdk\ValueObjects\Simple\Cpf;
use AsaasPhpSdk\ValueObjects\Simple\Email;
use AsaasPhpSdk\ValueObjects\Simple\Phone;
use AsaasPhpSdk\ValueObjects\Simple\PostalCode;
use AsaasPhpSdk\ValueObjects\Structured\CreditCardHolderInfo;

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

dataset(
    'credit_card_holder_info_null_fields',
    [
        [[
            'name' => null,
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => '00000-000',
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]],
        [[
            'name' => 'John Doe',
            'email' => null,
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => '00000-000',
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]],
        [[
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => null,
            'postalCode' => '00000-000',
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]],
        [[
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => null,
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]],
        [[
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => '00000-000',
            'addressNumber' => null,
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]],
        [[
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => '00000-000',
            'addressNumber' => '12345',
            'phone' => null,
            'mobilePhone' => ' 11 98888-8888',
        ]],
    ]
);

describe('Credit Card Holder Info VO', function (): void {
    it('can be created from valid data', function (): void {
        $creditCardHolderInfo = CreditCardHolderInfo::fromArray([
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => '00000-000',
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]);

        expect($creditCardHolderInfo->name)->toBe('John Doe');
        expect($creditCardHolderInfo->email)->toBeInstanceOf(Email::class)
            ->and($creditCardHolderInfo->email->value())->toBe('john.doe@test.com');
        expect($creditCardHolderInfo->cpfCnpj)->toBeInstanceOf(Cpf::class)->and($creditCardHolderInfo->cpfCnpj->value())->toBe('82412118051');
        expect($creditCardHolderInfo->postalCode)->toBeInstanceOf(PostalCode::class)->and($creditCardHolderInfo->postalCode->formatted())->toBe('00000-000');
        expect($creditCardHolderInfo->addressNumber)->toBe('12345');
        expect($creditCardHolderInfo->phone)->toBeInstanceOf(Phone::class)->and($creditCardHolderInfo->phone->value())->toBe('11999999999');
        expect($creditCardHolderInfo->mobilePhone)->toBeInstanceOf(Phone::class)->and($creditCardHolderInfo->mobilePhone->value())->toBe('11988888888');
    });
    it('throws an exception when required fields are missing', function ($data): void {
        expect(fn () => CreditCardHolderInfo::fromArray($data))->toThrow(InvalidCreditCardHolderInfoException::class);
    })->with('credit_card_holder_info_missing_fields');

    it('throws an exception when required fields are null', function ($data): void {
        expect(fn () => CreditCardHolderInfo::fromArray($data))->toThrow(InvalidCreditCardHolderInfoException::class);
    })->with('credit_card_holder_info_null_fields');

    it('compares the equality of two CreditCardHolderInfo objects', function (): void {
        $creditCardHolderInfo1 = CreditCardHolderInfo::fromArray([
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => '00000-000',
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]);

        $creditCardHolderInfo2 = CreditCardHolderInfo::fromArray([
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => '00000-000',
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]);

        $creditCardHolderInfo3 = CreditCardHolderInfo::fromArray([
            'name' => 'John Doe 2',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '824.121.180-51',
            'postalCode' => '00000-000',
            'addressNumber' => '12345',
            'phone' => ' 11 99999-9999',
            'mobilePhone' => ' 11 98888-8888',
        ]);

        expect($creditCardHolderInfo1->equals($creditCardHolderInfo2))->toBeTrue();
        expect($creditCardHolderInfo1->equals($creditCardHolderInfo3))->toBeFalse();
    });
});
