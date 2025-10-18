<?php

use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;

describe('Delete Payment Action', function (): void {
	beforeEach(function (): void {
		$config = sandboxConfig();
		$this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
		$getCustomersResponse = $this->asaasClient->customer()->list([
			'limit' => 1,
			'cpfCnpj' => '00264272000107',
		]);

		if (empty($getCustomersResponse['data'])) {
			$createCustomerResponse = $this->asaasClient->customer()->create([
				'name' => 'Maria Oliveira',
				'cpfCnpj' => '00264272000107',
			]);
			$this->customerId = $createCustomerResponse['id'];
		} else {
			$this->customerId = $getCustomersResponse['data'][0]['id'];
		}

		$this->createPaymentResponse = $this->asaasClient->payment()->create([
			'customer' => $this->customerId,
			'value' => random_int(100, 1000),
			'dueDate' => date('Y-m-d'),
			'billingType' => BillingTypeEnum::Pix->value,
		]);
	});

	it('deletes a payment successfully', function (): void {
		$response = $this->asaasClient->payment()->delete($this->createPaymentResponse['id']);
		expect($response['id'])->toBe($this->createPaymentResponse['id'])
			->and($response['deleted'])->toBe(true);
	});

	it('throws an exception when the payment is not found (404)', function (): void {
		$this->asaasClient->payment()->delete('invalid-id');
	})->throws(NotFoundException::class, 'Resource not found');

	it('throws an exception when the payment ID is empty', function (): void {
		$this->asaasClient->payment()->delete('');
	})->throws(\InvalidArgumentException::class, 'Payment ID cannot be empty');
});
