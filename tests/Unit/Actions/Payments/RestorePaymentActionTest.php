<?php

use AsaasPhpSdk\Actions\Payments\RestorePaymentAction;
use AsaasPhpSdk\Support\Helpers\ResponseHandler;

describe('Restore Payment Action', function (): void {

    it('Restore a payment successfully (200)', function (): void {
        $client = mockClient([
            mockResponse([
                'object' => 'payment',
                'id' => 'pay_123',
                'dateCreated' => '2023-06-01T00:00:00.000Z',
                'amount' => 1000,
                'deleted' => false,
            ], 200),
        ]);

        $action = new RestorePaymentAction($client, new ResponseHandler);

        $result = $action->handle('pay_123');

        expect($result)->toBeArray()
            ->and($result['deleted'])->toBeFalse()
            ->and($result['id'])->toBe('pay_123');
    });

    it('throws NotFoundException on 404 error', function (): void {
        $client = mockClient([
            mockErrorResponse('Resource not found', 404),
        ]);

        $action = new RestorePaymentAction($client, new ResponseHandler);

        $action->handle('non-existent-id');
    })->throws(\AsaasPhpSdk\Exceptions\Api\NotFoundException::class, 'Resource not found');

    it('throws InvalidArgumentException on invalid ID', function (): void {
        $client = mockClient();

        $action = new RestorePaymentAction($client, new ResponseHandler);

        $action->handle('');
    })->throws(\InvalidArgumentException::class, 'Payment ID cannot be empty');
});
