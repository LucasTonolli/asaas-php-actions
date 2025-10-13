<?php

use AsaasPhpSdk\Exceptions\InvalidPostalCodeException;
use AsaasPhpSdk\ValueObjects\Simple\PostalCode;

describe('PostalCode Value Object', function (): void {
	it('can be created with a valid postal code', function (): void {
		$postalCode = PostalCode::from('12345678');
		expect($postalCode->value())->toBe('12345678');
		expect($postalCode->formatted())->toBe('12345-678');
	});

	it('cannot be created with an invalid postal code', function (): void {
		expect(fn() => PostalCode::from('1234567'))->toThrow(InvalidPostalCodeException::class, 'Postal code must contain exactly 8 digits');
	});

	it('compare postal code', function (): void {
		$postalCode1 = PostalCode::from('12345678');
		$postalCode2 = PostalCode::from('12345678');
		$postalCode3 = PostalCode::from('12345679');
		expect($postalCode1->equals($postalCode2))->toBeTrue();
		expect($postalCode1->equals($postalCode3))->toBeFalse();
	});
});
