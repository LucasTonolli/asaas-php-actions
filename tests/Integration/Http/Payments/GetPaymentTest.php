<?php

use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;

const PAYMENT_KEYS = [
    'object',
    'id',
    'dateCreated',
    'customer',
    'checkoutSession',
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
    'invoiceUrl',
    'invoiceNumber',
    'externalReference',
    'deleted',
    'anticipated',
    'anticipable',
    'creditDate',
    'estimatedCreditDate',
    'transactionReceiptUrl',
    'nossoNumero',
    'discount',
    'fine',
    'interest',
    'postalService',
    'escrow',
    'refunds',
];

describe('Get Payment', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);

        $getCustomersResponse = $this->asaasClient->customer()->list([
            'limit' => 1,
            'cpfCnpj' => '00264272000107',
        ]);

        if (empty($getCustomersResponse['data'])) {
            $createCustomerResponse = $this->asaasClient->customer()->create([
                'name' => 'Maria Oliveira',
                'cpfCnpj' => '00264272000107',
            ]);
            $this->customerId = $createCustomerResponse['id'];
        } else {
            $this->customerId = $getCustomersResponse['data'][0]['id'];
        }
    });

    it('retrieves a payment successfully (200)', function (): void {

        $createPaymentResponse = $this->asaasClient->payment()->create([
            'customer' => $this->customerId,
            'value' => 100,
            'billingType' => BillingTypeEnum::Pix->value,
            'dueDate' => date('Y-m-d'),
        ]);

        $response = $this->asaasClient->payment()->get($createPaymentResponse['id']);
        expect($response)->toBeArray()
            ->and($response['object'])->toBe('payment')
            ->and($response['id'])->toBe($createPaymentResponse['id'])
            ->and($response['customer'])->toBe($createPaymentResponse['customer'])
            ->and($response['value'])->toBe($createPaymentResponse['value'])
            ->and($response['billingType'])->toBe($createPaymentResponse['billingType'])
            ->and($response['dueDate'])->toBe($createPaymentResponse['dueDate']);
    });

    it('throws an exception when the payment is not found (404)', function (): void {
        expect(fn () => $this->asaasClient->payment()->get('invalid-id'))->toThrow(NotFoundException::class, 'Resource not found');
    });

    it('throws an exception when the payment ID is empty', function (): void {
        expect(fn () => $this->asaasClient->payment()->get(''))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });

    it('matches the expected response structure', function (): void {
        $createPaymentResponse = $this->asaasClient->payment()->create([
            'customer' => $this->customerId,
            'value' => 100,
            'billingType' => BillingTypeEnum::Pix->value,
            'dueDate' => date('Y-m-d'),
        ]);

        $response = $this->asaasClient->payment()->get($createPaymentResponse['id']);
        expect($response)->toBeArray()
            ->and($response['id'])->toBe($createPaymentResponse['id'])
            ->and($response)->toHaveKeys(PAYMENT_KEYS);
    });
});
