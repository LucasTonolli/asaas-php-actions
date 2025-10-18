<?php

use AsaasPhpSdk\Actions\Payments\GetPaymentAction;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Helpers\ResponseHandler;

describe('Get Payment Action', function (): void {

    it('retrives a payment successfully', function (): void {
        $paymentId = 'pay_123';

        $client = mockClient([
            mockResponse([
                'object' => 'payment',
                'id' => $paymentId,
                'customer' => 'cus_123',
                'value' => 150.75,
                'billingType' => 'Boleto',
                'dueDate' => '2025-12-31',
                'status' => 'PENDING',
            ], 200),
        ]);

        $action = new GetPaymentAction($client, new ResponseHandler);

        $result = $action->handle($paymentId);

        expect($result)->toBeArray()
            ->and($result['object'])->toBe('payment')
            ->and($result['id'])->toBe($paymentId)
            ->and($result['customer'])->toBe('cus_123')
            ->and($result['value'])->toBe(150.75)
            ->and($result['billingType'])->toBe('Boleto')
            ->and($result['dueDate'])->toBe('2025-12-31')
            ->and($result['status'])->toBe('PENDING');
    });

    it('throws NotFoundException on 404 error', function (): void {
        $paymentId = 'pay_123';

        $client = mockClient([
            mockErrorResponse('Payment not found', 404),
        ]);

        $action = new GetPaymentAction($client, new ResponseHandler);

        $action->handle($paymentId);
    })->throws(NotFoundException::class, 'Resource not found');

    it('throws InvalidArgumentException when ID is empty', function (): void {
        $client = mockClient([]);
        $action = new GetPaymentAction($client, new ResponseHandler);

        $action->handle('');
    })->throws(\InvalidArgumentException::class, 'Payment ID cannot be empty');
});
