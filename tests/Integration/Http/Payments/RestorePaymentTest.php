<?php

use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;

describe('Restore Payment', function (): void {
	beforeEach(function (): void {
		$config = sandboxConfig();
		$this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
	});

	it('restores a payment successfully', function (): void {
		$customerId = getDefaultCustomer();

		$createPaymentResponse = $this->asaasClient->payment()->create([
			'customer' => $customerId,
			'value' => random_int(100, 1000),
			'dueDate' => date('Y-m-d'),
			'billingType' => BillingTypeEnum::Pix->value,
		]);

		$this->asaasClient->payment()->delete($createPaymentResponse['id']);

		$response = $this->asaasClient->payment()->restore($createPaymentResponse['id']);
		expect($response['id'])->toBe($createPaymentResponse['id'])
			->and($response['deleted'])->toBe(false)
			->and($response)->toHaveKeys([
				'id',
				'customer',
				'value',
				'dueDate',
				'billingType',
				'status',
			]);
	});

	it('throws an exception when the payment is not found (404)', function (): void {
		$this->asaasClient->payment()->restore('pay_notfound');
	})->throws(NotFoundException::class, 'Resource not found');

	it('throws an exception when the payment ID is empty', function (): void {
		$this->asaasClient->payment()->restore('');
	})->throws(\InvalidArgumentException::class, 'Payment ID cannot be empty');
});
