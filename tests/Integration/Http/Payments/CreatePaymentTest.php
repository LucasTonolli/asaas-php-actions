<?php

use AsaasPhpSdk\AsaasClient;
use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

describe('Create Payment', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasClient($config);

        // Create a valid customer to associate with the payment
        $this->customer = $this->asaasClient->customer()->create([
            'name' => 'Payment Customer '.uniqid(),
            'cpfCnpj' => '898.879.660-88',
        ]);
    });

    afterEach(function (): void {
        $this->asaasClient->customer()->delete($this->customer['id']);
    });

    it('creates a payment successfully', function (): void {
        $response = $this->asaasClient->payment()->create([
            'customer' => $this->customer['id'],
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 150.75,
            'dueDate' => '2025-12-31',
            'description' => 'Integration test payment',
        ]);

        expect($response['id'])->not()->toBeEmpty()
            ->and($response['customer'])->toBe($this->customer['id'])
            ->and($response['value'])->toBe(150.75)
            ->and($response['billingType'])->toBe('BOLETO')
            ->and($response['status'])->toBe('PENDING');
    });

    it('fails to create a payment with invalid value', function (): void {
        expect(fn () => $this->asaasClient->payment()->create([
            'customer' => $this->customer['id'],
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => -100, // invalid
            'dueDate' => '2025-12-31',
        ]))->toThrow(ValidationException::class);
    });

    it('matches the expected response structure', function (): void {
        $response = $this->asaasClient->payment()->create([
            'customer' => $this->customer['id'],
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 200.50,
            'dueDate' => '2025-12-31',
            'description' => 'Snapshot structure test',
        ]);

        expect($response)->toHaveKeys([
            'object',
            'id',
            'dateCreated',
            'customer',
            'paymentLink',
            'value',
            'netValue',
            'originalValue',
            'interestValue',
            'description',
            'billingType',
            'status',
            'dueDate',
            'originalDueDate',
            'paymentDate',
            'clientPaymentDate',
            'installmentNumber',
            'invoiceUrl',
            'bankSlipUrl',
            'transactionReceiptUrl',
            'externalReference',
            'discount',
            'fine',
            'interest',
            'postalService',
        ]);

        expect($response['object'])->toBe('payment')
            ->and($response['id'])->toStartWith('pay_')
            ->and($response['value'])->toBe(200.5)
            ->and($response['billingType'])->toBe('BOLETO')
            ->and($response['status'])->toBe('PENDING');
    });
});
