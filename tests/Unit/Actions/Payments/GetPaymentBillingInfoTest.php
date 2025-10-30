<?php

use AsaasPhpSdk\Actions\Payments\GetPaymentBillingInfoAction;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Support\Helpers\ResponseHandler;

describe('Get Payment Billing Info Action', function (): void {
	it('retrieves a pix payment billing info successfully', function (): void {
		$paymentId = 'pay_123';

		$client = mockClient([
			mockResponse([
				'pix' => [
					'encodedImage' => 'iVBORw0KGgoAAAANSUhEUgAA...',
					'payload' => '0000',
					'expirationDate' => '2024-12-31 23:59:59',
					'description' => 'Payment for services',
				]
			], 200)
		]);

		$action = new GetPaymentBillingInfoAction($client, new ResponseHandler);

		$result = $action->handle($paymentId);

		expect($result)->toBeArray()
			->and($result)->toHaveKeys(['pix'])
			->and($result['pix'])->toHaveKeys(['encodedImage', 'payload', 'expirationDate', 'description']);
	});

	it('retrieves a boleto payment billing info successfully', function (): void {
		$paymentId = 'pay_123';

		$client = mockClient([
			mockResponse([
				'bankSlip' => [
					"identificationField" => "00190000090275928800021932978170187890000005000",
					"nossoNumero" => "6543",
					"barCode" => "00191878900000050000000002759288002193297817",
					"bankSlipUrl" => "https://www.asaas.com/b/pdf/080225913252",
					"daysAfterDueDateToRegistrationCancellation" => 1
				]
			], 200)
		]);

		$action = new GetPaymentBillingInfoAction($client, new ResponseHandler);

		$result = $action->handle($paymentId);

		expect($result)->toBeArray()
			->and($result)->toHaveKeys(['bankSlip'])
			->and($result['bankSlip'])->toHaveKeys(['barCode', 'nossoNumero', 'identificationField', 'bankSlipUrl', 'daysAfterDueDateToRegistrationCancellation']);
	});

	it('retrieves a credit card payment billing info successfully', function (): void {
		$paymentId = 'pay_123';

		$client = mockClient([
			mockResponse([
				'creditCard' => [
					"creditCardNumber" => "8829",
					"creditCardBrand" => "VISA",
					"creditCardToken" => "a75a1d98-c52d-4a6b-a413-71e00b193c99"
				]
			], 200)
		]);

		$action = new GetPaymentBillingInfoAction($client, new ResponseHandler);

		$result = $action->handle($paymentId);

		expect($result)->toBeArray()
			->and($result)->toHaveKeys(['creditCard'])
			->and($result['creditCard'])->toHaveKeys(['creditCardNumber', 'creditCardBrand', 'creditCardToken']);
	});

	it('throws NotFoundException on 404 error', function (): void {
		$paymentId = 'pay_123';

		$client = mockClient([
			mockResponse([], 404),
		]);

		$action = new GetPaymentBillingInfoAction($client, new ResponseHandler);

		$action->handle($paymentId);
	})->throws(NotFoundException::class);

	it('throws InvalidArgumentException when ID is empty', function (): void {
		$client = mockClient([]);
		$action = new GetPaymentBillingInfoAction($client, new ResponseHandler);

		$action->handle('');
	})->throws(\InvalidArgumentException::class, 'Payment ID cannot be empty');
});
