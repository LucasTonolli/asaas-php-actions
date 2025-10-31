<?php

use AsaasPhpSdk\Actions\Payments\UpdatePaymentAction;
use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\DTOs\Payments\UpdatePaymentDTO;
use AsaasPhpSdk\Support\Helpers\ResponseHandler;

describe('Update Payment Action', function (): void {

    it('update payment successfully', function (): void {
        $client = mockClient([
            mockResponse([
                'id' => 'pay_123',
                'customer' => 'cus_123',
                'value' => 1000,
                'status' => 'PENDING',
            ], 200),
        ]);

        $action = new UpdatePaymentAction($client, new ResponseHandler);

        $dto = UpdatePaymentDTO::fromArray([
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 1000,
            'dueDate' => '2025-12-31',
        ]);

        $result = $action->handle('pay_123', $dto);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe('pay_123')
            ->and($result['customer'])->toBe('cus_123')
            ->and($result['value'])->toBe(1000)
            ->and($result['status'])->toBe('PENDING');
    });

    it('throws an InvalidArgumentException when missing payment id', function (): void {
        $client = mockClient([
            mockResponse([
                'id' => 'pay_123',
                'customer' => 'cus_123',
                'value' => 1000,
                'status' => 'PENDING',
            ]),
        ], 200);

        $action = new UpdatePaymentAction($client, new ResponseHandler);

        $dto = UpdatePaymentDTO::fromArray([
            'billingType' => BillingTypeEnum::Boleto->value,
            'value' => 1000,
            'dueDate' => '2025-12-31',
        ]);

        expect(fn () => $action->handle('', $dto))->toThrow(\InvalidArgumentException::class);
    });
});
