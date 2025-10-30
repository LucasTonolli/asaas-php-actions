<?php

use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;

describe('Get Payment Billing Info', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
        $this->customerId = getDefaultCustomer();
    });

    it('retrieves a pix payment billing info successfully', function (): void {
        $createPaymentResponse = $this->asaasClient->payment()->create([
            'customer' => $this->customerId,
            'value' => 150,
            'billingType' => BillingTypeEnum::Pix->value,
            'dueDate' => date('Y-m-d'),
        ]);

        $response = $this->asaasClient->payment()->getBillingInfo($createPaymentResponse['id']);
        expect($response)->toBeArray()
            ->and($response)->toHaveKeys([
                'pix',
                'bankSlip',
                'creditCard',
            ])
            ->and($response['pix'])->toBeArray()
            ->and($response['pix'])->toHaveKeys([
                'expirationDate',
                'encodedImage',
                'payload',
            ])
            ->and($response['bankSlip'])->toBeNull()
            ->and($response['creditCard'])->toBeNull();
        $this->asaasClient->payment()->delete($createPaymentResponse['id']);
    });

    it('retrieves a Boleto payment billing info successfully', function (): void {
        $createPaymentResponse = $this->asaasClient->payment()->create([
            'customer' => $this->customerId,
            'value' => 150,
            'billingType' => BillingTypeEnum::Boleto->value,
            'dueDate' => date('Y-m-d'),
        ]);

        $response = $this->asaasClient->payment()->getBillingInfo($createPaymentResponse['id']);
        expect($response)->toBeArray()
            ->and($response)->toHaveKeys([
                'pix',
                'bankSlip',
                'creditCard',
            ])
            ->and($response['bankSlip'])->toBeArray()
            ->and($response['bankSlip'])->toHaveKeys([
                'barCode',
                'nossoNumero',
                'identificationField',
                'bankSlipUrl',
                'daysAfterDueDateToRegistrationCancellation',
            ])
            ->and($response['pix'])->toBeArray()
            ->and($response['creditCard'])->toBeNull();
        $this->asaasClient->payment()->delete($createPaymentResponse['id']);
    });

    it('retrieves a credit card payment billing info, after a successful payment, successfully', function (): void {
        $createPaymentResponse = $this->asaasClient->payment()->create([
            'customer' => $this->customerId,
            'value' => 150,
            'billingType' => BillingTypeEnum::CreditCard->value,
            'dueDate' => date('Y-m-d'),
        ]);

        $this->asaasClient->payment()->chargeWithCreditCard(
            $createPaymentResponse['id'],
            [
                'creditCard' => [
                    'holderName' => 'John Doe',
                    'number' => '4111111111111111',
                    'expiryMonth' => '12',
                    'expiryYear' => (string) ((int) date('Y') + 1),
                    'ccv' => '123',
                ],
                'creditCardHolderInfo' => [
                    'name' => 'John Doe',
                    'email' => 'john.doe@test.com',
                    'cpfCnpj' => '824.121.180-51',
                    'postalCode' => '00000-000',
                    'phone' => '1234567890',
                    'addressNumber' => '123',
                ],
            ]
        );

        $response = $this->asaasClient->payment()->getBillingInfo($createPaymentResponse['id']);
        expect($response)->toBeArray()
            ->and($response)->toHaveKeys([
                'pix',
                'bankSlip',
                'creditCard',
            ])
            ->and($response['creditCard'])->toBeArray()
            ->and($response['creditCard'])->toHaveKeys([
                'creditCardNumber',
                'creditCardBrand',
                'creditCardToken',
            ])
            ->and($response['pix'])->toBeNull()
            ->and($response['bankSlip'])->toBeNull();
    });

    it('throws an exception when the payment is not found (404)', function (): void {
        expect(fn () => $this->asaasClient->payment()->getBillingInfo('invalid-id'))->toThrow(NotFoundException::class, 'Resource not found');
    });

    it('throws an exception when the payment ID is empty', function (): void {
        expect(fn () => $this->asaasClient->payment()->getBillingInfo(' '))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });
});
