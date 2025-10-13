<?php

use AsaasPhpSdk\Exceptions\InvalidInterestException;
use AsaasPhpSdk\ValueObjects\Interest;

describe('Interest', function (): void {
	it('can be created with a valid interest', function (): void {
		$interest = Interest::create(10.0);
		expect($interest->value)->toBe(10.0);
	});

	it('cannot be created with an invalid interest', function (): void {
		expect(fn() => Interest::create(-1.0))->toThrow(InvalidInterestException::class, 'Interest value cannot be negative');
		expect(fn() => Interest::create(101.0))->toThrow(InvalidInterestException::class, 'Interest value cannot exceed 100%');
	});

	it('value is required', function (): void {
		expect(fn() => Interest::fromArray([]))->toThrow(InvalidInterestException::class, 'Interest value is required');
	});

	it('compares the same interest', function (): void {
		$interest1 = Interest::create(10.0);
		$interest2 = Interest::create(10.0);
		$interest3 = Interest::create(20.0);
		expect($interest1->equals($interest2))->toBeTrue();
		expect($interest1->equals($interest3))->toBeFalse();
	});
});
