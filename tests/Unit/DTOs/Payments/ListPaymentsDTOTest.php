<?php

use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\DTOs\Payments\Enums\InvoiceStatusEnum;
use AsaasPhpSdk\DTOs\Payments\ListPaymentsDTO;
use AsaasPhpSdk\DTOs\Payments\Enums\PaymentStatusEnum;
use AsaasPhpSdk\Exceptions\InvalidDateRangeException;

dataset('payments_filters', [
	[
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
		[
			'key' => 'dateCreatedStart',
			'value' => '2023-01-01',
		],
		[
			'key' => 'dateCreatedEnd',
			'value' => '2023-01-01',
		],
		[
			'key' => 'paymentDateStart',
			'value' => '2023-01-01',
		],
		[
			'key' => 'paymentDateEnd',
			'value' => '2023-01-01',
		],
		[
			'key' => 'dueDateStart',
			'value' => '2023-01-01',
		],
		[
			'key' => 'dueDateEnd',
			'value' => '2023-01-01',
		]
	],
]);

dataset('payments_filters_invalid_values', [
	[
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
	]
]);

dataset('payments_filters_values_to_be_fixed', [
	[
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
	]
]);

dataset('payments_filters_error_values', [
	[
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
	]
]);

dataset('payments_invalid_range_values', [
	[
		[
			'start-key' => 'dateCreatedStart',
			'start-value' => '2023-01-02',
			'end-key' => 'dateCreatedEnd',
			'end-value' => '2023-01-01',
		],
		[
			'start-key' => 'paymentDateStart',
			'start-value' => '2023-01-02',
			'end-key' => 'paymentDateEnd',
			'end-value' => '2023-01-01',
		],
		[
			'start-key' => 'dueDateStart',
			'start-value' => '2023-01-02',
			'end-key' => 'dueDateEnd',
			'end-value' => '2023-01-01',
		],
	]
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
	'dateCreatedStart',
	'dateCreatedEnd',
	'paymentDateStart',
	'paymentDateEnd',
	'dueDateStart',
	'dueDateEnd',
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

	it('filters fields', function ($args): void {

		$dto = ListPaymentsDTO::fromArray([
			$args['key'] => $args['value'],
		]);

		expect($dto->toArray())->toHaveKey($args['key'])
			->and($dto->toArray()[$args['key']])->toBe($args['value']);
		expect($dto->toArray())->not()->toHaveKeys(array_filter(PAYMENT_FILTER_KEYS, fn(string $key): bool => $key !== $args['key']));
	})->with('payments_filters');

	it('filters fields with invalid values become null', function ($args): void {
		$dto = ListPaymentsDTO::fromArray([
			$args['key'] => $args['value'],
		]);

		expect($dto->toArray())->not()->toHaveKey($args['key']);
	})->with('payments_filters_invalid_values');

	it('fixes filter values', function ($args): void {
		$dto = ListPaymentsDTO::fromArray([
			$args['key'] => $args['value'],
		]);

		expect($dto->toArray())->toHaveKey($args['key'])
			->and($dto->toArray()[$args['key']])->toBe($args['expected']);
	})->with('payments_filters_values_to_be_fixed');

	it('throws an exception for invalid values', function ($args): void {
		expect(fn() => ListPaymentsDTO::fromArray([
			$args['key'] => $args['value'],
		]))->toThrow(DateMalformedStringException::class);
	})->with('payments_filters_error_values');

	it('throws an exception for invalid range values', function ($args): void {
		expect(fn() => ListPaymentsDTO::fromArray([
			$args['start-key'] => $args['start-value'],
			$args['end-key'] => $args['end-value'],
		]))->toThrow(InvalidDateRangeException::class, "The \"{$args['start-key']}\" must be before \"{$args['end-key']}\"");
	})->with('payments_invalid_range_values');
});
