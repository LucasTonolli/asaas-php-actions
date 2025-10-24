<?php

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidInterestException;
use AsaasPhpSdk\ValueObjects\Structured\Interest;

describe('Interest', function (): void {
    it('can be created with a valid interest', function (): void {
        $interest = Interest::fromArray([
            'value' => 10,
        ]);
        expect($interest->value)->toBe(10.0);

        $interest = Interest::fromArray([
            'value' => 10.0,
        ]);

        expect($interest->value)->toBe(10.0);
    });

    it('cannot be created with an invalid interest', function (): void {
        expect(fn () => Interest::fromArray(['value' => -5]))->toThrow(InvalidInterestException::class, 'Interest value cannot be negative');
        expect(fn () => Interest::fromArray(['value' => 150]))->toThrow(InvalidInterestException::class, 'Interest value cannot exceed 100%');
    });

    it('value is required', function (): void {
        expect(fn () => Interest::fromArray([]))->toThrow(InvalidInterestException::class, 'Interest value is required');
    });

    it('compares the same interest', function (): void {
        $interest1 = Interest::fromArray([
            'value' => 10.0,
        ]);
        $interest2 = Interest::fromArray([
            'value' => 10.0,
        ]);
        $interest3 = Interest::fromArray([
            'value' => 5.0,
        ]);
        expect($interest1->equals($interest2))->toBeTrue();
        expect($interest1->equals($interest3))->toBeFalse();
    });
});
