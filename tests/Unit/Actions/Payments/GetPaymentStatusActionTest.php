<?php

use AsaasPhpSdk\Actions\Payments\GetPaymentStatusAction;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Helpers\ResponseHandler;

describe('Get Payment Status Action', function (): void {
	it('retrieves payment status successfully', function (): void {
		$paymentId = 'pay_456';

		$client = mockClient([
			mockResponse([
				'status' => 'PAID',
			], 200),
		]);

		$action = new GetPaymentStatusAction($client, new ResponseHandler);

		$result = $action->handle($paymentId);

		expect($result)->toBeArray()
			->and($result['status'])->toBe('PAID');
	});

	it('throws NotFoundException on 404 error', function (): void {
		$paymentId = 'pay_456';

		$client = mockClient([
			mockResponse([], 404),
		]);

		$action = new GetPaymentStatusAction($client, new ResponseHandler);

		$action->handle($paymentId);
	})->throws(NotFoundException::class, 'Resource not found');

	it('throws InvalidArgumentException when ID is empty', function (): void {
		$client = mockClient([]);
		$action = new GetPaymentStatusAction($client, new ResponseHandler);

		$action->handle('');
	})->throws(\InvalidArgumentException::class, 'Payment ID cannot be empty');
});
