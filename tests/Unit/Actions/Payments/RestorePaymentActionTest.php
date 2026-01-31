<?php

use AsaasPhpSdk\Actions\Payments\RestorePaymentAction;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('RestorePaymentAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new RestorePaymentAction($this->transporter);
    });

    it('restores a payment successfully (200)', function (): void {
        $paymentId = 'pay_123';
        $expectedData = [
            'object' => 'payment',
            'id' => $paymentId,
            'dateCreated' => '2023-06-01T00:00:00.000Z',
            'amount' => 1000,
            'deleted' => false,
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('POST', 'payments/' . $paymentId . '/restore', [])
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId);

        expect($result)->toBeArray()
            ->and($result['deleted'])->toBeFalse()
            ->and($result['id'])->toBe($paymentId);
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn() => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });
});
