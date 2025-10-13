<?php

use AsaasPhpSdk\Enums\DiscountType;
use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidDiscountException;
use AsaasPhpSdk\ValueObjects\Structured\Discount;

describe('Discount Value Object', function (): void {
	it('can be created with a valid discount', function (): void {
		$discount = Discount::create(10.0, 30, 'percentage');
		expect($discount->value)->toBe(10.0);
		expect($discount->dueDateLimitDays)->toBe(30);
		expect($discount->discountType)->toBe(DiscountType::Percentage);

		$discount = Discount::fromArray([
			'value' => 10.0,
			'dueDateLimitDays' => 30,
			'type' => 'fixed',
		]);

		expect($discount->value)->toBe(10.0);
		expect($discount->dueDateLimitDays)->toBe(30);
		expect($discount->discountType)->toBe(DiscountType::Fixed);
	});

	it('cannot be created with an invalid discount', function (): void {
		expect(fn() => Discount::create(-1.0, 30, 'percentage'))->toThrow(InvalidDiscountException::class, 'Value must be greater than 0.');
		expect(fn() => Discount::create(101.0, 30, 'percentage'))->toThrow(InvalidDiscountException::class, 'Discount percentage cannot exceed 100%');
		expect(fn() => Discount::create(10.0, 30, 'invalid'))->toThrow(InvalidDiscountException::class, 'Invalid discount type');
	});

	it('value is required', function (): void {
		expect(fn() => Discount::fromArray([]))->toThrow(InvalidDiscountException::class, 'Discount value is required');

		expect(fn() => Discount::fromArray([
			'value' => 10.0
		]))->toThrow(InvalidDiscountException::class, 'Discount dueDateLimitDays is required');
	});

	it('compares the same discount', function (): void {
		$discount1 = Discount::create(10.0, 30, 'percentage');
		$discount2 = Discount::create(10.0, 30, 'percentage');
		$discount3 = Discount::create(10.0, 30, 'fixed');
		expect($discount1->equals($discount2))->toBeTrue();
		expect($discount1->equals($discount3))->toBeFalse();
	});
});
