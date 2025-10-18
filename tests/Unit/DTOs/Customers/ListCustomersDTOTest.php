<?php

use AsaasPhpSdk\DTOs\Customers\ListCustomersDTO;
use AsaasPhpSdk\ValueObjects\Simple\Cnpj;
use AsaasPhpSdk\ValueObjects\Simple\Cpf;
use AsaasPhpSdk\ValueObjects\Simple\Email;

const CUSTOMER_FILTER_KEYS = ['limit', 'offset', 'name', 'email', 'cpfCnpj', 'groupName', 'externalReference'];

dataset('customers_filters', [
    [
        'key' => 'limit',
        'value' => 10,
    ],
    [
        'key' => 'offset',
        'value' => 0,
    ],
    [
        'key' => 'name',
        'value' => 'John Doe',
    ],
    [
        'key' => 'email',
        'value' => 'john.doe@test.com',
    ],
    [
        'key' => 'cpfCnpj',
        'value' => '89887966088',
    ],
    [
        'key' => 'groupName',
        'value' => 'VIP',
    ],
    [
        'key' => 'externalReference',
        'value' => 'REF123',
    ],
]);

dataset('customers_filters_invalid_values', [
    [
        'key' => 'limit',
        'value' => 'a',
    ],
    [
        'key' => 'offset',
        'value' => 'b',
    ],
    [
        'key' => 'name',
        'value' => '  ',
    ],
    [
        'key' => 'email',
        'value' => 'invalid',
    ],
    [
        'key' => 'cpfCnpj',
        'value' => '123456789',
    ],
    [
        'key' => 'groupName',
        'value' => '  ',
    ],
    [
        'key' => 'externalReference',
        'value' => '  ',
    ]
]);

describe('List Customers DTO', function (): void {

    it('has the correct structure', function (): void {
        $dto = ListCustomersDTO::fromArray([
            'limit' => 10,
            'offset' => 0,
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'cpfCnpj' => '898.879.660-88',
            'groupName' => 'VIP',
            'externalReference' => 'REF123',
        ]);

        expect($dto->limit)->toBe(10);
        expect($dto->offset)->toBe(0);
        expect($dto->name)->toBe('John Doe');
        expect($dto->email)->toBeInstanceOf(Email::class)
            ->and($dto->email->value())->toBe('john.doe@test.com');
        expect($dto->cpfCnpj)->toBeInstanceOf(Cpf::class)
            ->and($dto->cpfCnpj->value())->toBe('89887966088');
        expect($dto->groupName)->toBe('VIP');
        expect($dto->externalReference)->toBe('REF123');
    });

    it('filters fields', function ($key, $value): void {

        $dto = ListCustomersDTO::fromArray([
            $key => $value,
        ]);
        expect($dto->toArray())->toHaveKey($key)
            ->and($dto->toArray()[$key])->toBe($value);
        expect($dto->toArray())->not()->toHaveKeys(array_filter(CUSTOMER_FILTER_KEYS, fn(string $filterKey): bool => $filterKey !== $key));
    })->with('customers_filters');

    it('handles null and missing fields', function (): void {
        $dto = ListCustomersDTO::fromArray([]);

        expect($dto->limit)->toBeNull();
        expect($dto->offset)->toBeNull();
        expect($dto->name)->toBeNull();
        expect($dto->email)->toBeNull();
        expect($dto->cpfCnpj)->toBeNull();
        expect($dto->groupName)->toBeNull();
        expect($dto->externalReference)->toBeNull();
    });

    it('filter fields with invalid values become null', function ($key, $value): void {

        $dto = ListCustomersDTO::fromArray([
            $key => $value,
        ]);

        expect($dto->toArray())->not()->toHaveKeys([
            $key,
        ]);
    })->with('customers_filters_invalid_values');

    it('handles CPF and CNPJ correctly', function (): void {

        $dtoCpf = ListCustomersDTO::fromArray([
            'cpfCnpj' => '898.879.660-88',
        ]);
        expect($dtoCpf->cpfCnpj)->toBeInstanceOf(Cpf::class);

        $dtoCnpj = ListCustomersDTO::fromArray([
            'cpfCnpj' => '12.345.678/0001-95',
        ]);
        expect($dtoCnpj->cpfCnpj)->toBeInstanceOf(Cnpj::class);

        $dtoInvalid = ListCustomersDTO::fromArray([
            'cpfCnpj' => '123456789',
        ]);
        expect($dtoInvalid->cpfCnpj)->toBeNull();

        $dtoInvalidFormat = ListCustomersDTO::fromArray([
            'cpfCnpj' => '111.111.111-11',
        ]);
        expect($dtoInvalidFormat->cpfCnpj)->toBeNull();
    });

    it('toArray returns only non-null fields', function (): void {
        $dto = ListCustomersDTO::fromArray([
            'name' => 'John',
            'cpfCnpj' => '898.879.660-88',
        ]);

        $array = $dto->toArray();

        expect($array)->toHaveKeys(['name', 'cpfCnpj'])
            ->and($array)->not->toHaveKey('email')
            ->and($array)->not->toHaveKey('offset')
            ->and($array)->not->toHaveKey('limit')
            ->and($array)->not->toHaveKey('groupName')
            ->and($array)->not->toHaveKey('externalReference');
    });
});
