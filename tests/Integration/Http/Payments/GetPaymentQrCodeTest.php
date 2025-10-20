<?php

use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

describe('Get Payment QR Code', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig(true);
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
    });

    it('retrieves a payment QR code successfully', function (): void {
        $createPaymentResponse = $this->asaasClient->payment()->create([
            'customer' => getDefaultCustomer(),
            'value' => 150,
            'billingType' => BillingTypeEnum::Pix->value,
            'dueDate' => date('Y-m-d'),
        ]);

        $response = $this->asaasClient->payment()->getQrCode($createPaymentResponse['id']);
        expect($response)->toBeArray()
            ->and($response)->toHaveKeys([
                'encodedImage',
                'payload',
                'expirationDate',
                'description',
            ]);
        $this->asaasClient->payment()->delete($createPaymentResponse['id']);
    })->skip('[Issue #49] Requires pix key available');

    it('throws NotFoundException for non-existent payment ID', function (): void {
        $this->asaasClient->payment()->getQrCode('non_existent_id');
    })->throws(NotFoundException::class);

    it('throws InvalidArgumentException for empty payment ID', function (): void {
        $this->asaasClient->payment()->getQrCode('');
    })->throws(InvalidArgumentException::class);

    it('throws ValidationException for invalid billing type format', function (): void {
        $createPaymentResponse = $this->asaasClient->payment()->create([
            'customer' => getDefaultCustomer(),
            'value' => 150,
            'billingType' => BillingTypeEnum::CreditCard->value,
            'dueDate' => date('Y-m-d'),
        ]);

        expect(fn () => $this->asaasClient->payment()->getQrCode($createPaymentResponse['id']))->toThrow(ValidationException::class, 'Somente é possível obter QR Code quando a forma de pagamento for PIX.');
        $this->asaasClient->payment()->delete($createPaymentResponse['id']);
    })->skip('[Issue #49] Requires pix key available');
});
