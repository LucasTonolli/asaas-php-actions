<?php

use AsaasPhpSdk\Exceptions\InvalidPhoneException;
use AsaasPhpSdk\ValueObjects\Simple\Phone;

describe('Phone Value Object', function (): void {
	it('can be created with a valid phone number', function (): void {
		$phone = Phone::from('11999999999');
		expect($phone->value())->toBe('11999999999');
		expect($phone->formatted())->toBe('(11) 99999-9999');
	});

	it('cannot be created with an invalid phone number', function (): void {
		expect(fn() => Phone::from('123456789'))->toThrow(InvalidPhoneException::class, 'Phone must contain 10 or 11 digits');
	});

	it('compare phone', function (): void {
		$phone1 = Phone::from('11999999999');
		$phone2 = Phone::from('11999999999');
		$phone3 = Phone::from('11999999998');
		expect($phone1->equals($phone2))->toBeTrue();
		expect($phone1->equals($phone3))->toBeFalse();
	});
});
