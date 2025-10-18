<?php

use AsaasPhpSdk\AsaasClient;
use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;

describe('List Payments', function (): void {

    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasClient($config);

        $this->customer = $this->asaasClient->customer()->create([
            'name' => 'John Doe '.uniqid(),
            'cpfCnpj' => '898.879.660-88',
        ]);

        $this->payment = $this->asaasClient->payment()->create([
            'customer' => $this->customer['id'],
            'billingType' => BillingTypeEnum::CreditCard->value,
            'value' => 500,
            'dueDate' => date('Y-m-d'),
            'description' => 'Test payment '.uniqid(),
        ]);
    });

    afterEach(function (): void {
        $this->asaasClient->customer()->delete($this->customer['id']);
    });

    it('lists and filters payments successfully', function (): void {

        $filters = [
            'customer' => $this->customer['id'],
            'billingType' => BillingTypeEnum::CreditCard->value,
        ];

        $response = $this->asaasClient->payment()->list($filters);

        expect($response)->not()->toBeEmpty()
            ->and($response['object'])->toBe('list')
            ->and($response['totalCount'])->toBe(1)
            ->and($response['data'])->toHaveCount(1);

        $foundPayment = $response['data'][0];

        expect($foundPayment['id'])->toBe($this->payment['id'])
            ->and($foundPayment['customer'])->toBe($this->customer['id'])
            ->and($foundPayment['value'])->toBe(500); // Check the exact value
    });

    it('matches the expected response structure', function (): void {
        $response = $this->asaasClient->payment()->list();

        expect($response)->toHaveKeys([
            'object',
            'totalCount',
            'limit',
            'offset',
            'hasMore',
            'data',
        ]);
    });
});
