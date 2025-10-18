<?php

use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;

describe('Get Payment Status', function (): void {
	beforeEach(function (): void {
		$config = sandboxConfig();
		$this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
	});

	it('retrieves a payment status successfully', function (): void {
		$createPaymentResponse = $this->asaasClient->payment()->create([
			'customer' => getDefaultCustomer(),
			'value' => 150,
			'billingType' => BillingTypeEnum::CreditCard->value,
			'dueDate' => date('Y-m-d'),
		]);

		$response = $this->asaasClient->payment()->getStatus($createPaymentResponse['id']);
		expect($response)->toBeArray()
			->and($response['status'])->toBe('PENDING')
			->and($response)->not()->toHaveKey('id');

		$this->asaasClient->payment()->delete($createPaymentResponse['id']);
	});

	it('throws an exception when the payment is not found (404)', function (): void {
		expect(fn() => $this->asaasClient->payment()->getStatus('invalid-id'))->toThrow(\Exception::class, 'Resource not found');
	});

	it('throws an exception when the payment ID is empty', function (): void {
		expect(fn() => $this->asaasClient->payment()->getStatus(''))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
	});
});
