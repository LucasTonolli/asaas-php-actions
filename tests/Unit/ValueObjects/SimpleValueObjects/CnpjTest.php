<?php

use AsaasPhpSdk\Exceptions\InvalidCnpjException;
use AsaasPhpSdk\ValueObjects\Simple\Cnpj;

describe('Cnpj Value Object', function (): void {
	it('can be created with a valid CNPJ with formatting', function (): void {
		$cnpj = Cnpj::from('85.312.108/0001-72');
		expect($cnpj->value())->toBe('85312108000172');
		expect($cnpj->formatted())->toBe('85.312.108/0001-72');
	});

	it('can be created with a valid CNPJ without formatting', function (): void {
		$cnpj = Cnpj::from('85312108000172');
		expect($cnpj->value())->toBe('85312108000172');
		expect($cnpj->formatted())->toBe('85.312.108/0001-72');
	});

	it('cannot be created with an invalid CNPJ', function (): void {
		expect(fn() => Cnpj::from('11111111111111'))->toThrow(InvalidCnpjException::class, 'Invalid Cnpj: 11111111111111');
	});

	it('compare cnpj', function (): void {
		$cnpj1 = Cnpj::from('85312108000172');
		$cnpj2 = Cnpj::from('85312108000172');
		$cnpj3 = Cnpj::from('42.240.295/0001-13');
		expect($cnpj1->equals($cnpj2))->toBeTrue();
		expect($cnpj1->equals($cnpj3))->toBeFalse();
	});
});
