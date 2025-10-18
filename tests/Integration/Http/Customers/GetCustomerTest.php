<?php

describe('Get Customer', function (): void {

    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
    });

    it('retrieves a customer successfully (200)', function (): void {
        $customerId = getDefaultCustomer();

        $response = $this->asaasClient->customer()->get($customerId);
        expect($response)->toBeArray()
            ->and($response['id'])->toBe($customerId)
            ->and($response['name'])->toBe('Maria Oliveira')
            ->and($response['cpfCnpj'])->toBe('00264272000107')
            ->and($response)->toHaveKeys(CUSTOMER_KEYS);
    });

    it('throws an exception when the customer is not found (404)', function (): void {
        expect(fn () => $this->asaasClient->customer()->get('invalid-customer-id'))->toThrow(AsaasPhpSdk\Exceptions\Api\NotFoundException::class, 'Resource not found');
    });

    it('throws an exception when the customer ID is empty', function (): void {
        expect(fn () => $this->asaasClient->customer()->get(''))->toThrow(\InvalidArgumentException::class, 'Customer ID cannot be empty');
    });

    it('matches the expected response structure', function (): void {
        $customerId = getDefaultCustomer();

        $response = $this->asaasClient->customer()->get($customerId);
        expect($response['id'])->toBe($customerId);
        expect($response)->toHaveKeys(CUSTOMER_KEYS);
    });
});
