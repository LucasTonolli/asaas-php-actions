<?php

use AsaasPhpSdk\Actions\Payments\GetPaymentStatusAction;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('GetPaymentStatusAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new GetPaymentStatusAction($this->transporter);
    });

    it('retrieves payment status successfully (200)', function (): void {
        $paymentId = 'pay_456';
        $expectedData = [
            'status' => 'PAID',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'payments/' . $paymentId . '/status', [])
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId);

        expect($result)->toBeArray()
            ->and($result['status'])->toBe('PAID');
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn() => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });
});
