<?php

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidCreditCardException;
use AsaasPhpSdk\ValueObjects\Structured\CreditCard;

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

dataset('credit_card_null_fields', [
    [[
        'holderName' => null,
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => '2025',
        'cvv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'number' => null,
        'expiryMonth' => '12',
        'expiryYear' => '2025',
        'cvv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'number' => '4111111111111111',
        'expiryMonth' => null,
        'expiryYear' => '2025',
        'cvv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => null,
        'cvv' => '123',
    ]],
    [[
        'holderName' => 'John Doe',
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => '2025',
        'cvv' => null,
    ]],
]);

describe('Credit Card Value Object', function (): void {
    it('can be created with valid credit card data', function (): void {
        $creditCard = CreditCard::fromArray([
            'holderName' => 'John Doe',
            'number' => '4769 9981 1166 8248',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '123',
        ]);
        expect($creditCard->holderName)->toBe('John Doe');
        expect($creditCard->number)->toBe('4769998111668248');
        expect($creditCard->expiryMonth)->toBe('12');
        expect($creditCard->expiryYear)->toBe('2025');
        expect($creditCard->cvv)->toBe('123');
    });

    it('formats expiration month with leading zero', function (): void {
        $creditCard = CreditCard::fromArray([
            'holderName' => 'John Doe',
            'number' => '4769 9981 1166 8248',
            'expiryMonth' => '5',
            'expiryYear' => '2026',
            'cvv' => '123',
        ]);
        expect($creditCard->expiryMonth)->toBe('05');
    });

    it('throws exception when missing required fields', function ($creditCard): void {
        $keys = ['holderName', 'number', 'expiryMonth', 'expiryYear', 'cvv'];
        $missingFields = array_diff($keys, array_keys($creditCard));
        $missingField = current($missingFields);

        expect(fn() => CreditCard::fromArray($creditCard))
            ->toThrow(InvalidCreditCardException::class, 'Missing required field:' . " {$missingField}");
    })->with('credit_card_missing_fields');

    it('throws exception when required fields are null', function ($creditCard): void {
        $missingField = array_keys(array_filter($creditCard, 'is_null'))[0];

        expect(fn() => CreditCard::fromArray($creditCard))
            ->toThrow(InvalidCreditCardException::class, 'Missing required field:' . " {$missingField}");
    })->with('credit_card_null_fields');

    it('cannot be created with invalid expiration month', function (): void {
        expect(fn() => CreditCard::fromArray([
            'holderName' => 'John Doe',
            'number' => '4111111111111111',
            'expiryMonth' => '13',
            'expiryYear' => '2025',
            'cvv' => '123',
        ]))->toThrow(InvalidCreditCardException::class, 'Expiration month must be between 01 and 12');
    });

    it('cannot be created with past expiration year', function (): void {
        expect(fn() => CreditCard::fromArray([
            'holderName' => 'John Doe',
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2020',
            'cvv' => '123',
        ]))->toThrow(InvalidCreditCardException::class, 'Expiration year cannot be in the past');
    });

    it('cannot be create with invalid number', function (): void {
        expect(fn() => CreditCard::fromArray([
            'holderName' => 'John Doe',
            'number' => '1234567890123456',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '123',
        ]))->toThrow(InvalidCreditCardException::class, 'Invalid credit card number');
    });

    it('cannot be create with invalid cvv', function (): void {
        expect(fn() => CreditCard::fromArray([
            'holderName' => 'John Doe',
            'number' => '4769 9981 1166 8248',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '12345',
        ]))->toThrow(InvalidCreditCardException::class, 'CVV must be 3 or 4 digits');

        expect(fn() => CreditCard::fromArray([
            'holderName' => 'John Doe',
            'number' => '4769 9981 1166 8248',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '12',
        ]))->toThrow(InvalidCreditCardException::class, 'CVV must be 3 or 4 digits');
    });

    it('cannot be create with invalid expiration year format', function (): void {
        expect(fn() => CreditCard::fromArray([
            'holderName' => 'John Doe',
            'number' => '4769 9981 1166 8248',
            'expiryMonth' => '12',
            'expiryYear' => '25',
            'cvv' => '123',
        ]))->toThrow(InvalidCreditCardException::class, 'Expiration year must be 4 digits (YYYY)');
    });

    it('compares the same credit card', function (): void {
        $creditCard1 = CreditCard::fromArray([
            'holderName' => 'John Doe',
            'number' => '4769 9981 1166 8248',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '123',
        ]);
        $creditCard2 = CreditCard::fromArray([
            'holderName' => 'John Doe',
            'number' => '4769 9981 1166 8248',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '123',
        ]);
        $creditCard3 = CreditCard::fromArray([
            'holderName' => 'John Doe',
            'number' => '4769 9981 1166 8248',
            'expiryMonth' => '11',
            'expiryYear' => '2026',
            'cvv' => '456',
        ]);
        expect($creditCard1->equals($creditCard2))->toBeTrue();
        expect($creditCard1->equals($creditCard3))->toBeFalse();
    });
});
