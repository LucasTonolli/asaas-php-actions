<?php

use AsaasPhpSdk\Actions\Payments\GetPaymentAction;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('GetPaymentAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new GetPaymentAction($this->transporter);
    });

    it('retrieves a payment successfully (200)', function (): void {
        $paymentId = 'pay_123';
        $expectedData = [
            'object' => 'payment',
            'id' => $paymentId,
            'customer' => 'cus_123',
            'value' => 150.75,
            'billingType' => 'Boleto',
            'dueDate' => '2025-12-31',
            'status' => 'PENDING',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'payments/'.$paymentId, [])
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId);

        expect($result)->toBeArray()
            ->and($result['object'])->toBe('payment')
            ->and($result['id'])->toBe($paymentId)
            ->and($result['customer'])->toBe('cus_123')
            ->and($result['value'])->toBe(150.75)
            ->and($result['billingType'])->toBe('Boleto')
            ->and($result['dueDate'])->toBe('2025-12-31')
            ->and($result['status'])->toBe('PENDING');
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn () => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });
});
