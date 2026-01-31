<?php

use AsaasPhpSdk\Actions\Payments\DeletePaymentAction;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('DeletePaymentAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new DeletePaymentAction($this->transporter);
    });

    it('deletes a payment successfully (200)', function (): void {
        $paymentId = 'pay_123';
        $expectedData = [
            'deleted' => true,
            'id' => $paymentId,
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('DELETE', 'payments/' . $paymentId, [])
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId);

        expect($result)->toBeArray()
            ->and($result['deleted'])->toBeTrue()
            ->and($result['id'])->toBe($paymentId);
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn() => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });
});
