<?php

use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\DTOs\Payments\Enums\InvoiceStatusEnum;
use AsaasPhpSdk\DTOs\Payments\ListPaymentsDTO;
use AsaasPhpSdk\DTOs\Payments\Enums\PaymentStatusEnum;
use AsaasPhpSdk\Exceptions\InvalidDateRangeException;

dataset('payments_filters', [

	[
		'key' => 'installment',
		'value' => 'xxxxx',
	],
	[
		'key' => 'offset',
		'value' => 10,
	],
	[
		'key' => 'limit',
		'value' => 5,
	],
	[
		'key' => 'customer',
		'value' => 'cus_123',
	],
	[
		'key' => 'customerGroupName',
		'value' => 'group_123',
	],
	[
		'key' => 'billingType',
		'value' => BillingTypeEnum::Boleto->value,
	],
	[
		'key' => 'status',
		'value' => PaymentStatusEnum::Pending->value,
	],
	[
		'key' => 'subscription',
		'value' => 'sub_123',
	],
	[
		'key' => 'externalReference',
		'value' => 'ref_123',
	],
	[
		'key' => 'paymentDate',
		'value' => '2023-01-01',
	],
	[
		'key' => 'invoiceStatus',
		'value' => InvoiceStatusEnum::Scheduled->value,
	],
	[
		'key' => 'anticipated',
		'value' => true,
	],
	[
		'key' => 'anticipable',
		'value' => true,
	],
]);

dataset('payments_filters_custom_keys', [
	[
		'attr' => 'dateCreatedStart',
		'key' => 'dateCreated[ge]',
		'value' => '2023-01-01',
	],
	[
		'attr' => 'dateCreatedEnd',
		'key' => 'dateCreated[le]',
		'value' => '2023-01-01',
	],
	[
		'attr' => 'paymentDateStart',
		'key' => 'paymentDate[ge]',
		'value' => '2023-01-01',
	],
	[
		'attr' => 'paymentDateEnd',
		'key' => 'paymentDate[le]',
		'value' => '2023-01-01',
	],
	[
		'attr' => 'dueDateStart',
		'key' => 'dueDate[ge]',
		'value' => '2023-01-01',
	],
	[
		'attr' => 'dueDateEnd',
		'key' => 'dueDate[le]',
		'value' => '2023-01-01',
	]
]);

dataset('payments_filters_invalid_values', [

	[
		'key' => 'offset',
		'value' => -10,
	],
	[
		'key' => 'installment',
		'value' => '  ',
	],
	[
		'key' => 'customer',
		'value' => ' ',
	],
	[
		'key' => 'customerGroupName',
		'value' => '  ',
	],
	[
		'key' => 'billingType',
		'value' => 'invalid',
	],
	[
		'key' => 'status',
		'value' => 'invalid',
	],
	[
		'key' => 'subscription',
		'value' => '  ',
	],
	[
		'key' => 'externalReference',
		'value' => '  ',
	],
	[
		'key' => 'invoiceStatus',
		'value' => 'invalid',
	],
	[
		'key' => 'anticipated',
		'value' => 'invalid',
	],
	[
		'key' => 'anticipable',
		'value' => 'invalid',
	],
	[
		'key' => 'paymentDate',
		'value' => 'invalid',
	],
	[
		'key' => 'dateCreatedStart',
		'value' => 'invalid',
	],
	[
		'key' => 'dateCreatedEnd',
		'value' => 'invalid',
	],
	[
		'key' => 'paymentDateStart',
		'value' => 'invalid',
	],
	[
		'key' => 'paymentDateEnd',
		'value' => 'invalid',
	],
	[
		'key' => 'dueDateStart',
		'value' => 'invalid',
	],
	[
		'key' => 'dueDateEnd',
		'value' => 'invalid',
	]

]);

dataset('payments_filters_values_to_be_fixed', [

	[
		'key' => 'limit',
		'value' => -5,
		'expected' => 1,
	],
	[
		'key' => 'limit',
		'value' => 102,
		'expected' => 100,
	]
]);

dataset('payments_filters_error_values', [
	[
		'key' => 'paymentDate',
		'value' => 'invalid',
	],
	[
		'key' => 'dateCreatedStart',
		'value' => 'invalid',
	],
	[
		'key' => 'dateCreatedEnd',
		'value' => 'invalid',
	],
	[
		'key' => 'paymentDateStart',
		'value' => 'invalid',
	],
	[
		'key' => 'paymentDateEnd',
		'value' => 'invalid',
	],
	[
		'key' => 'dueDateStart',
		'value' => 'invalid',
	],
	[
		'key' => 'dueDateEnd',
		'value' => 'invalid',
	],
]);

dataset('payments_invalid_range_values', [
	[
		'dateCreatedStart',
		'2023-01-02',
		'dateCreatedEnd',
		'2023-01-01',
	],
	[
		'paymentDateStart',
		'2023-01-02',
		'paymentDateEnd',
		'2023-01-01',
	],
	[
		'dueDateStart',
		'2023-01-02',
		'dueDateEnd',
		'2023-01-01',
	],
]);

const PAYMENT_FILTER_KEYS = [
	'installment',
	'offset',
	'limit',
	'customer',
	'customerGroupName',
	'billingType',
	'status',
	'subscription',
	'externalReference',
	'paymentDate',
	'invoiceStatus',
	'anticipated',
	'anticipable',
	'dateCreated[ge]',
	'dateCreated[le]',
	'paymentDate[ge]',
	'paymentDate[le]',
	'dueDate[ge]',
	'dueDate[le]',
];

describe('List Payments DTO', function (): void {

	it('has the correct structure', function (): void {
		$dto = ListPaymentsDTO::fromArray([
			'limit' => 5,
			'offset' => 0,
		]);

		expect($dto->limit)->toBe(5);
		expect($dto->offset)->toBe(0);
		expect($dto->toArray())->not()->toHaveKeys([
			'customer',
			'customerGroupName',
			'billingType',
			'status',
		]);
	});

	it('filters fields', function ($key, $value): void {

		$dto = ListPaymentsDTO::fromArray([
			$key => $value,
		]);
		expect($dto->toArray())->toHaveKey($key)
			->and($dto->toArray()[$key])->toBe($value);
		expect($dto->toArray())->not()->toHaveKeys(array_diff(PAYMENT_FILTER_KEYS, [$key]));
	})->with('payments_filters');

	it('filters fields with custom keys', function ($attr, $key, $value): void {

		$dto = ListPaymentsDTO::fromArray([
			$attr => $value,
		]);
		expect($dto->toArray())->toHaveKey($key)
			->and($dto->toArray()[$key])->toBe($value);
		expect($dto->toArray())->not()->toHaveKeys(array_filter(PAYMENT_FILTER_KEYS, fn(string $filterKey): bool => $filterKey !== $key));
	})->with('payments_filters_custom_keys');

	it('filters fields with invalid values become null', function ($key, $value): void {
		$dto = ListPaymentsDTO::fromArray([
			$key => $value,
		]);
		expect($dto->toArray())->not()->toHaveKey($key);
	})->with('payments_filters_invalid_values');

	it('fixes filter values', function ($key, $value, $expected): void {
		$dto = ListPaymentsDTO::fromArray([
			$key => $value,
		]);

		expect($dto->toArray())->toHaveKey($key)
			->and($dto->toArray()[$key])->toBe($expected);
	})->with('payments_filters_values_to_be_fixed');

	it('throws an exception for invalid range values', function ($startKey, $startValue, $endKey, $endValue): void {
		expect(fn() => ListPaymentsDTO::fromArray([
			$startKey => $startValue,
			$endKey => $endValue,
		]))->toThrow(InvalidDateRangeException::class, "The \"{$startKey}\" must be before \"{$endKey}\"");
	})->with('payments_invalid_range_values');

	it('toArray return only non-null fields', function (): void {
		$dto = ListPaymentsDTO::fromArray([
			'limit' => 5,
			'offset' => 0,
			'dateCreatedStart' => '2023-01-01',
			'dateCreatedEnd' => '2023-01-02',
		]);

		expect($dto->toArray())->toHaveKeys([
			'limit',
			'offset',
			'dateCreated[ge]',
			'dateCreated[le]',
		])
			->and($dto->toArray())->not()->toHaveKeys([
				'installment',
				'customer',
				'customerGroupName',
				'billingType',
				'status',
				'subscription',
				'externalReference',
				'paymentDate',
				'invoiceStatus',
				'anticipated',
				'anticipable',
				'paymentDate[ge]',
				'paymentDate[le]',
				'dueDate[ge]',
				'dueDate[le]',
			]);
	});
});
