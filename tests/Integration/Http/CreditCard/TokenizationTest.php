<?php

describe('Tokenization', function (): void {
	beforeEach(function (): void {
		$config = sandboxConfig();
		$this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
		$this->customerId = getDefaultCustomer();
	});
	it('tokenizes a credit card successfully', function (): void {
		$data = [
			'creditCard' => [
				'holderName' => 'John Doe',
				'number' => '4242 4242 4242 4242',
				'expiryMonth' => '12',
				'expiryYear' => (string) ((int) date('Y') + 1),
				'ccv' => '123',
			],
			'customer' => $this->customerId,
			'remoteIp' => '127.0.0.1',
			'creditCardHolderInfo' => [
				'name' => 'John Doe',
				'email' => 'john.doe@test.com',
				'cpfCnpj' => '824.121.180-51',
				'postalCode' => '01310000',
				'addressNumber' => '12345',
				'phone' => '1234567890',
			]
		];

		$response = $this->asaasClient->creditCard()->tokenize($data);
		expect($response)->toBeArray()
			->and($response)->toHaveKeys([
				'creditCardToken',
				'creditCardNumber',
				'creditCardBrand',
			]);
	});
});
