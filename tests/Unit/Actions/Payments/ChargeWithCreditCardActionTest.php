<?php

use AsaasPhpSdk\Actions\Payments\ChargeWithCreditCardAction;
use AsaasPhpSdk\DTOs\Payments\ChargeWithCreditCardDTO;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\Helpers\ResponseHandler;

describe('Charge With Credit Card Action', function (): void {
	it('charges a payment with credit card successfully', function (): void {
		$client = mockClient([
			mockResponse([
				'id' => 'pay_123',
				'customer' => 'cus_123',
				'value' => 150.75,
				'billingType' => 'CREDIT_CARD',
				'dueDate' => '2025-12-31',
				'status' => 'COMPLETE',
			])
		]);

		$action = new ChargeWithCreditCardAction($client, new ResponseHandler);
		$dto = ChargeWithCreditCardDTO::fromArray([
			'creditCard' => [
				'holderName' => 'John Doe',
				'number' => '4111111111111111',
				'expirationMonth' => '12',
				'expirationYear' => '2025',
				'cvv' => '123',
			],
			'creditCardHolderInfo' => [
				'name' => 'John Doe',
				'email' => 'john.doe@test.com',
				'cpfCnpj' => '824.121.180-51',
				'postalCode' => '00000-000',
				'phone' => '1234567890',
				'addressNumber' => '123',
			]
		]);

		$result = $action->handle('pay_123', $dto);

		expect($result)->toBeArray()
			->and($result['id'])->toBe('pay_123')
			->and($result['customer'])->toBe('cus_123')
			->and($result['value'])->toBe(150.75)
			->and($result['billingType'])->toBe('CREDIT_CARD')
			->and($result['dueDate'])->toBe('2025-12-31')
			->and($result['status'])->toBe('COMPLETE');
	});

	it('throws NotFoundException on 404 error', function (): void {
		$client = mockClient([
			mockErrorResponse('Payment not found', 404),
		]);

		$action = new ChargeWithCreditCardAction($client, new ResponseHandler);
		$dto = ChargeWithCreditCardDTO::fromArray([
			'creditCard' => [
				'holderName' => 'John Doe',
				'number' => '4111111111111111',
				'expirationMonth' => '12',
				'expirationYear' => '2025',
				'cvv' => '123',
			],
			'creditCardHolderInfo' => [
				'name' => 'John Doe',
				'email' => 'john.doe@test.com',
				'cpfCnpj' => '824.121.180-51',
				'postalCode' => '00000-000',
				'phone' => '1234567890',
				'addressNumber' => '123',
			]
		]);

		$action->handle('pay_123', $dto);
	})->throws(NotFoundException::class);

	it('throws InvalidArgumentException when ID is empty', function (): void {
		$client = mockClient();

		$action = new ChargeWithCreditCardAction($client, new ResponseHandler);
		$dto = ChargeWithCreditCardDTO::fromArray([
			'creditCard' => [
				'holderName' => 'John Doe',
				'number' => '4111111111111111',
				'expirationMonth' => '12',
				'expirationYear' => '2025',
				'cvv' => '123',
			],
			'creditCardHolderInfo' => [
				'name' => 'John Doe',
				'email' => 'john.doe@test.com',
				'cpfCnpj' => '824.121.180-51',
				'postalCode' => '00000-000',
				'phone' => '1234567890',
				'addressNumber' => '123',
			]
		]);

		$action->handle('', $dto);
	})->throws(\InvalidArgumentException::class, 'Payment ID cannot be empty');
});
