<?php

use AsaasPhpSdk\Actions\Payments\GetPaymentQrCodeAction;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Helpers\ResponseHandler;

describe('Get Payment Qr Code Action', function (): void {
	it('retrieves payment QR code successfully', function (): void {
		$paymentId = 'pay_789';

		$client = mockClient([
			mockResponse([
				'encodedImage' => 'iVBORw0KGgoAAAANSUhEUgAA...',
				'payload' => '00020101021226730014br.gov.bcb.pix2551pix-h.asaas.com/pixqrcode/cobv/pay_76575613967995145204000053039865802BR5905ASAAS6009Joinville61088902SC62070503***63041D3D',
				'expirationDate' => '2024-12-31 23:59:59',
				'description' => 'Payment for services',
			], 200),
		]);

		$action = new GetPaymentQrCodeAction($client, new ResponseHandler);

		$result = $action->handle($paymentId);

		expect($result)->toBeArray()
			->and($result)->toHaveKeys(['encodedImage', 'payload', 'expirationDate', 'description']);
	});

	it('throws NotFoundException on 404 error', function (): void {
		$paymentId = 'pay_456';

		$client = mockClient([
			mockResponse([], 404),
		]);

		$action = new GetPaymentQrCodeAction($client, new ResponseHandler);

		$action->handle($paymentId);
	})->throws(NotFoundException::class, 'Resource not found');

	it('throws InvalidArgumentException when ID is empty', function (): void {
		$client = mockClient([]);
		$action = new GetPaymentQrCodeAction($client, new ResponseHandler);

		$action->handle('');
	})->throws(\InvalidArgumentException::class, 'Payment ID cannot be empty');
});
