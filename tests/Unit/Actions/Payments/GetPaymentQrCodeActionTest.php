<?php

use AsaasPhpSdk\Actions\Payments\GetPaymentQrCodeAction;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('GetPaymentQrCodeAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new GetPaymentQrCodeAction($this->transporter);
    });

    it('retrieves payment QR code successfully (200)', function (): void {
        $paymentId = 'pay_789';
        $expectedData = [
            'encodedImage' => 'iVBORw0KGgoAAAANSUhEUgAA...',
            'payload' => '00020101021226730014br.gov.bcb.pix2551pix-h.asaas.com/pixqrcode/cobv/pay_76575613967995145204000053039865802BR5905ASAAS6009Joinville61088902SC62070503***63041D3D',
            'expirationDate' => '2024-12-31 23:59:59',
            'description' => 'Payment for services',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'payments/'.$paymentId.'/pixQrCode', [])
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId);

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['encodedImage', 'payload', 'expirationDate', 'description']);
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn () => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });
});
