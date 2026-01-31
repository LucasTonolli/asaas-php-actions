<?php

use AsaasPhpSdk\Actions\Payments\UpdatePaymentAction;
use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\DTOs\Payments\UpdatePaymentDTO;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('UpdatePaymentAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new UpdatePaymentAction($this->transporter);
    });

    it('updates payment successfully (200)', function (): void {
        $paymentId = 'pay_123';
        $dto = UpdatePaymentDTO::fromArray([
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 1000,
            'dueDate' => '2025-12-31',
        ]);

        $expectedData = [
            'id' => $paymentId,
            'customer' => 'cus_123',
            'value' => 1000,
            'status' => 'PENDING',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('PUT', 'payments/'.$paymentId, $dto->toArray())
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId, $dto);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe($paymentId)
            ->and($result['customer'])->toBe('cus_123')
            ->and($result['value'])->toBe(1000)
            ->and($result['status'])->toBe('PENDING');
    });

    it('throws InvalidArgumentException when payment ID is empty', function (): void {
        $dto = UpdatePaymentDTO::fromArray([
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 1000,
            'dueDate' => '2025-12-31',
        ]);

        expect(fn () => $this->action->handle('', $dto))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });
});
