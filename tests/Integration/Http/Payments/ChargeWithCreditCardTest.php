<?php

use AsaasPhpSdk\DTOs\Payments\ChargeWithCreditCardDTO;
use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

describe('Charge With Credit Card', function (): void {
	beforeEach(function (): void {
		$config = sandboxConfig();
		$this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
		$this->customerId = getDefaultCustomer();
	});

	it('charges a payment successfully (200)', function (): void {
		$createPaymentResponse = $this->asaasClient->payment()->create([
			'customer' => $this->customerId,
			'value' => 100,
			'billingType' => BillingTypeEnum::CreditCard->value,
			'dueDate' => date('Y-m-d'),
		]);

		$dto = ChargeWithCreditCardDTO::fromArray([
			'creditCard' => [
				'holderName' => 'John Doe',
				'number' => '4111111111111111',
				'expiryMonth' => '12',
				'expiryYear' =>  (string) ((int) date('Y') + 1),
				'ccv' => '123',
			],
			'creditCardHolderInfo' => [
				'name' => 'John Doe',
				'email' => 'john.doe@test.com',
				'cpfCnpj' => '824.121.180-51',
				'postalCode' => '00000-000',
				'phone' => '1234567890',
				'addressNumber' => '123',
			],
		]);

		$response = $this->asaasClient->payment()->chargeWithCreditCard(
			$createPaymentResponse['id'],
			$dto
		);
		expect($response)->toBeArray()
			->and($response['object'])->toBe('payment')
			->and($response['id'])->toBe($createPaymentResponse['id'])
			->and($response['customer'])->toBe($createPaymentResponse['customer'])
			->and($response['billingType'])->toBe($createPaymentResponse['billingType'])
			->and($response['dueDate'])->toBe($createPaymentResponse['dueDate'])
			->and($response['status'])->toBe('CONFIRMED');
	});

	it('throws ValidationException when the payment is not found (400)', function (): void {
		$dto = ChargeWithCreditCardDTO::fromArray([
			'creditCard' => [
				'holderName' => 'John Doe',
				'number' => '4111111111111111',
				'expiryMonth' => '12',
				'expiryYear' =>  (string) ((int) date('Y') + 1),
				'ccv' => '123',
			],
			'creditCardHolderInfo' => [
				'name' => 'John Doe',
				'email' => 'john.doe@test.com',
				'cpfCnpj' => '824.121.180-51',
				'postalCode' => '00000-000',
				'phone' => '1234567890',
				'addressNumber' => '123',
			],
		]);


		expect(fn() => $this->asaasClient->payment()->chargeWithCreditCard(
			'payment_not_found',
			$dto
		))->toThrow(ValidationException::class, 'Cobrança inexistente.');
	});
});
