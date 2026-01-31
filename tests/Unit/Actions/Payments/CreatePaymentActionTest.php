<?php

use AsaasPhpSdk\Actions\Payments\CreatePaymentAction;
use AsaasPhpSdk\DTOs\Payments\CreatePaymentDTO;
use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('CreatePaymentAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new CreatePaymentAction($this->transporter);
    });

    it('creates payment successfully (201)', function (): void {
        $dto = CreatePaymentDTO::fromArray([
            'customer' => 'cus_123',
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 150.75,
            'dueDate' => '2025-12-31',
            'description' => 'Test payment',
        ]);

        $expectedData = [
            'id' => 'pay_123',
            'customer' => 'cus_123',
            'value' => 150.75,
            'billingType' => 'Boleto',
            'dueDate' => '2025-12-31',
            'status' => 'PENDING',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('POST', 'payments', $dto->toArray())
            ->andReturn($expectedData);

        $result = $this->action->handle($dto);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe('pay_123')
            ->and($result['customer'])->toBe('cus_123')
            ->and($result['value'])->toBe(150.75)
            ->and($result['billingType'])->toBe('Boleto')
            ->and($result['status'])->toBe('PENDING');
    });

    it('throws InvalidPaymentDataException when DTO validation fails', function (): void {
        expect(fn () => CreatePaymentDTO::fromArray([
            'customer' => 'cus_123',
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 100,
            // intentionally missing dueDate
        ]))->toThrow(InvalidPaymentDataException::class, "Required field 'dueDate' is missing.");
    });
});
