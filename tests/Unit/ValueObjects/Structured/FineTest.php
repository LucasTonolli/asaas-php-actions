<?php

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidFineException;
use AsaasPhpSdk\ValueObjects\Structured\Enums\FineType;
use AsaasPhpSdk\ValueObjects\Structured\Fine;

describe('Fine Value Object', function (): void {
    it('can be created with a valid fine', function (): void {
        $fine = Fine::fromArray([
            'value' => 10.0,
            'type' => 'percentage',
        ]);
        expect($fine->value)->toBe(10.0);
        expect($fine->type)->toBe(FineType::Percentage);

        $fine = Fine::fromArray([
            'value' => 10.0,
            'type' => 'fixed',
        ]);
        expect($fine->value)->toBe(10.0);
        expect($fine->type)->toBe(FineType::Fixed);
    });

    it('cannot be created with an invalid fine', function (): void {
        expect(fn() => Fine::fromArray([
            'value' => -5.0,
            'type' => 'percentage',
        ]))->toThrow(InvalidFineException::class, 'Fine value cannot be negative');
        expect(fn() => Fine::fromArray([
            'value' => 150.0,
            'type' => 'percentage',
        ]))->toThrow(InvalidFineException::class, 'Fine percentage cannot exceed 100%');
        expect(fn() => Fine::fromArray([
            'value' => 10.0,
            'type' => 'invaldtype',
        ]))->toThrow(InvalidFineException::class, 'Invalid fine type');
    });

    it('value is required', function (): void {
        expect(fn() => Fine::fromArray([]))->toThrow(InvalidFineException::class, 'Fine value is required');
    });

    it('compares the same fine', function (): void {
        $fine1 = Fine::fromArray([
            'value' => 10.0,
            'type' => 'percentage',
        ]);
        $fine2 = Fine::fromArray([
            'value' => 10.0,
            'type' => 'percentage',
        ]);
        $fine3 = Fine::fromArray([
            'value' => 5.0,
            'type' => 'fixed',
        ]);
        expect($fine1->equals($fine2))->toBeTrue();
        expect($fine1->equals($fine3))->toBeFalse();
    });
});
