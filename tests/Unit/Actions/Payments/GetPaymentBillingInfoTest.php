<?php

use AsaasPhpSdk\Actions\Payments\GetPaymentBillingInfoAction;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('GetPaymentBillingInfoAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new GetPaymentBillingInfoAction($this->transporter);
    });

    it('retrieves a pix payment billing info successfully (200)', function (): void {
        $paymentId = 'pay_123';
        $expectedData = [
            'pix' => [
                'encodedImage' => 'iVBORw0KGgoAAAANSUhEUgAA...',
                'payload' => '0000',
                'expirationDate' => '2024-12-31 23:59:59',
                'description' => 'Payment for services',
            ],
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'payments/'.$paymentId.'/billingInfo', [])
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId);

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['pix'])
            ->and($result['pix'])->toHaveKeys(['encodedImage', 'payload', 'expirationDate', 'description']);
    });

    it('retrieves a boleto payment billing info successfully (200)', function (): void {
        $paymentId = 'pay_123';
        $expectedData = [
            'bankSlip' => [
                'identificationField' => '00190000090275928800021932978170187890000005000',
                'nossoNumero' => '6543',
                'barCode' => '00191878900000050000000002759288002193297817',
                'bankSlipUrl' => 'https://www.asaas.com/b/pdf/080225913252',
                'daysAfterDueDateToRegistrationCancellation' => 1,
            ],
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'payments/'.$paymentId.'/billingInfo', [])
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId);

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['bankSlip'])
            ->and($result['bankSlip'])->toHaveKeys(['barCode', 'nossoNumero', 'identificationField', 'bankSlipUrl', 'daysAfterDueDateToRegistrationCancellation']);
    });

    it('retrieves a credit card payment billing info successfully (200)', function (): void {
        $paymentId = 'pay_123';
        $expectedData = [
            'creditCard' => [
                'creditCardNumber' => '8829',
                'creditCardBrand' => 'VISA',
                'creditCardToken' => 'a75a1d98-c52d-4a6b-a413-71e00b193c99',
            ],
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'payments/'.$paymentId.'/billingInfo', [])
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId);

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['creditCard'])
            ->and($result['creditCard'])->toHaveKeys(['creditCardNumber', 'creditCardBrand', 'creditCardToken']);
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn () => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });
});
