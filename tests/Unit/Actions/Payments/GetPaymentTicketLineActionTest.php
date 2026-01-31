<?php

use AsaasPhpSdk\Actions\Payments\GetPaymentTicketLineAction;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('GetPaymentTicketLineAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new GetPaymentTicketLineAction($this->transporter);
    });

    it('retrieves payment ticket line successfully (200)', function (): void {
        $paymentId = 'pay_123';
        $expectedData = [
            'identificationField' => '1234567890',
            'nossoNumero' => '0987654321',
            'barCode' => '00190500954014481606906809350314337370000000100',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'payments/'.rawurlencode($paymentId).'/identificationField', [])
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId);

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys([
                'identificationField',
                'nossoNumero',
                'barCode',
            ]);
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn () => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });
});
