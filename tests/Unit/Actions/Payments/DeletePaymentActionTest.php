<?php

use AsaasPhpSdk\Actions\Payments\DeletePaymentAction;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Helpers\ResponseHandler;

describe('Delete Payment Action', function (): void {
	it('deletes a payment successfully', function (): void {
		$client = mockClient([
			mockResponse([
				'deleted' => true,
				'id' => 'pay_123',
			])
		]);

		$action = new DeletePaymentAction($client, new ResponseHandler);

		$result = $action->handle('pay_123');

		expect($result)->toBeArray()
			->and($result['deleted'])->toBeTrue()
			->and($result['id'])->toBe('pay_123');
	});

	it('throws NotFoundException on 404 error', function (): void {
		$client = mockClient([
			mockErrorResponse('Payment not found', 404),
		]);

		$action = new DeletePaymentAction($client, new ResponseHandler);

		$action->handle('non-existent-id');
	})->throws(NotFoundException::class, 'Resource not found');

	it('throws InvalidArgumentException when ID is empty', function (): void {
		$client = mockClient();
		$action = new DeletePaymentAction($client, new ResponseHandler);

		$action->handle('');
	})->throws(\InvalidArgumentException::class, 'Payment ID cannot be empty');
});
