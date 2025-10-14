<?php

use AsaasPhpSdk\Exceptions\ValueObjects\Simple\InvalidCpfException;
use AsaasPhpSdk\ValueObjects\Simple\Cpf;

describe('Cpf Value Object', function (): void {
    it('can be created with a valid Cpf with formatting', function (): void {
        $cpf = Cpf::from('199.003.930-82');
        expect($cpf->value())->toBe('19900393082');
        expect($cpf->formatted())->toBe('199.003.930-82');
    });

    it('can be created with a valid CPF without formatting', function (): void {
        $cpf = Cpf::from('96682822057');
        expect($cpf->value())->toBe('96682822057');
        expect($cpf->formatted())->toBe('966.828.220-57');
    });

    it('cannot be created with an invalid CPF', function (): void {
        expect(fn () => Cpf::from('11111111111'))->toThrow(InvalidCpfException::class, 'Invalid CPF: 11111111111');
    });

    it('compare cpf', function (): void {
        $cpf1 = Cpf::from('07462524040');
        $cpf2 = Cpf::from('07462524040');
        $cpf3 = Cpf::from('014.781.080-96');
        expect($cpf1->equals($cpf2))->toBeTrue();
        expect($cpf1->equals($cpf3))->toBeFalse();
    });
});
