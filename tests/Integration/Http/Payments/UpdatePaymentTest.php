<?php

use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

describe('Update Payment', function (): void {
	beforeEach(function (): void {
		$config = sandboxConfig();
		$this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
		$this->customerId = getDefaultCustomer();
	});

	it('updates a payment successfully', function (): void {
		$createPaymentResponse = $this->asaasClient->payment()->create([
			'customer' => $this->customerId,
			'billingType' => BillingTypeEnum::Boleto->value,
			'value' => 150.75,
			'dueDate' => (string) ((int) date('Y') + 1) . '-12-31',
			'description' => 'Integration test payment',
		]);

		$response = $this->asaasClient->payment()->update($createPaymentResponse['id'], [
			'billingType' => BillingTypeEnum::Boleto->value,
			'value' => 200,
			'dueDate' => '2025-12-31',
		]);

		expect($response['value'])->toBe(200.0)
			->and($response)->toHaveKeys([
				'object',
				'id',
				'dateCreated',
				'customer',
				'value',
				'billingType',
				'status',
				'dueDate',
				'originalDueDate',
				'dueDate',
			]);
		$this->asaasClient->payment()->delete($createPaymentResponse['id']);
	});

	it('throws an exception when try to update a payment already CONFIRMED', function (): void {

		$createPaymentResponse = $this->asaasClient->payment()->create([
			'customer' => $this->customerId,
			'billingType' => BillingTypeEnum::CreditCard->value,
			'value' => 150.75,
			'dueDate' => (string) ((int) date('Y') + 1) . '-12-31',
			'description' => 'Integration test payment',
		]);

		$this->asaasClient->payment()->chargeWithCreditCard($createPaymentResponse['id'], [
			'creditCard' => [
				'holderName' => 'John Doe',
				'number' => '4111111111111111',
				'expiryMonth' => '12',
				'expiryYear' => (string) ((int) date('Y') + 1),
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

		$response = $this->asaasClient->payment()->update($createPaymentResponse['id'], [
			'billingType' => BillingTypeEnum::Boleto->value,
			'value' => 200,
			'dueDate' => '2025-12-31',
		]);
	})->throws(ValidationException::class, 'Não é possível alterar a forma de pagamento de cobranças recebidas ou confirmadas.');

	it('throws an exception when the payment is not found (404)', function (): void {

		$this->asaasClient->payment()->update('invalid-id', [
			'billingType' => BillingTypeEnum::Boleto->value,
			'value' => 200,
			'dueDate' => '2025-12-31',
		]);
	})->throws(NotFoundException::class, 'Resource not found');

	it('throws an exception when the payment ID is empty', function (): void {
		$this->asaasClient->payment()->update(' ', [
			'billingType' => BillingTypeEnum::Boleto->value,
			'value' => 200,
			'dueDate' => '2025-12-31',
		]);
	})->throws(\InvalidArgumentException::class, 'Payment ID cannot be empty');
});
