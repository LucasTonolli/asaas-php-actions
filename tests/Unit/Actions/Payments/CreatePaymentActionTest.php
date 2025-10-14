<?php

declare(strict_types=1);

use AsaasPhpSdk\Actions\Payments\CreatePaymentAction;
use AsaasPhpSdk\DTOs\Payments\CreatePaymentDTO;
use AsaasPhpSdk\DTOs\Payments\BillingTypeEnum;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;

describe('Create Payment Action', function (): void {

	it('creates payment successfully', function (): void {
		$client = mockClient([
			mockResponse([
				'id' => 'pay_123',
				'customer' => 'cus_123',
				'value' => 150.75,
				'billingType' => 'Boleto',
				'dueDate' => '2025-12-31',
				'status' => 'PENDING',
			], 201),
		]);

		$action = new CreatePaymentAction($client, new ResponseHandler);

		$dto = CreatePaymentDTO::fromArray([
			'customer' => 'cus_123',
			'billingType' => BillingTypeEnum::Boleto->value,
			'value' => 150.75,
			'dueDate' => '2025-12-31',
			'description' => 'Test payment',
		]);

		$result = $action->handle($dto);

		expect($result)->toBeArray()
			->and($result['id'])->toBe('pay_123')
			->and($result['customer'])->toBe('cus_123')
			->and($result['value'])->toBe(150.75)
			->and($result['billingType'])->toBe('Boleto')
			->and($result['status'])->toBe('PENDING');
	});

	it('throws InvalidPaymentDataException when DTO validation fails', function (): void {
		CreatePaymentDTO::fromArray([
			'customer' => 'cus_123',
			'billingType' => BillingTypeEnum::Boleto->value,
			'value' => 100,
			// intentionally missing dueDate
		]);
	})->throws(InvalidPaymentDataException::class, "Required field 'dueDate' is missing.");

	it('throws ValidationException on API 400 error', function (): void {
		$client = mockClient([
			mockErrorResponse('Input validation failed', 400, [
				['description' => 'API validation error'],
			]),
		]);

		$action = new CreatePaymentAction($client, new ResponseHandler);

		$dto = CreatePaymentDTO::fromArray([
			'customer' => 'cus_123',
			'billingType' => BillingTypeEnum::Boleto->value,
			'value' => 100,
			'dueDate' => '2025-12-31',
		]);

		$action->handle($dto);
	})->throws(AsaasPhpSdk\Exceptions\Api\ValidationException::class);

	it('throws ApiException on network connection error', function (): void {
		$mock = new MockHandler([
			new ConnectException(
				'Connection failed',
				new Request('POST', 'payments')
			),
		]);

		$handlerStack = HandlerStack::create($mock);
		$client = new Client(['handler' => $handlerStack]);

		$action = new CreatePaymentAction($client, new ResponseHandler);

		$dto = CreatePaymentDTO::fromArray([
			'customer' => 'cus_123',
			'billingType' => BillingTypeEnum::Boleto->value,
			'value' => 100,
			'dueDate' => '2025-12-31',
		]);

		$action->handle($dto);
	})->throws(ApiException::class, 'Failed to connect to Asaas API: Connection failed');
});
