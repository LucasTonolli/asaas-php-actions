<?php

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidSplitException;
use AsaasPhpSdk\ValueObjects\Structured\Split;
use AsaasPhpSdk\ValueObjects\Structured\SplitEntry;

describe('Split Value Object', function (): void {
	it('can be created with an array of entries', function (): void {
		$split = Split::fromArray([
			[
				'walletId' => 'wallet_id',
				'fixedValue' => 10.0,
			],
			[
				'walletId' => 'wallet_id',
				'percentageValue' => 14.0,
			]
		]);
		expect($split->entriesToArray())->toBe([
			[
				'walletId' => 'wallet_id',
				'fixedValue' => 10.0,
			],
			[
				'walletId' => 'wallet_id',
				'percentageValue' => 14.0
			]
		]);

		$split = Split::create([
			SplitEntry::fromArray([
				'walletId' => 'wallet_id',
				'fixedValue' => 10.0,
			])
		]);

		expect($split->entriesToArray())->toBe([
			[
				'walletId' => 'wallet_id',
				'fixedValue' => 10.0,
			]
		]);
	});

	it('cannot be created with an invalid array of entries', function (): void {
		expect(fn() => Split::fromArray([]))->toThrow(InvalidSplitException::class, 'Split entries must not be empty');
	});

	it('sums fixed and percentage values', function (): void {
		$split = Split::fromArray([
			[
				'walletId' => 'wallet_id',
				'fixedValue' => 10.0,
			],
			[
				'walletId' => 'wallet_id_2',
				'percentageValue' => 14.0,
			],
			[
				'walletId' => 'wallet_id',
				'percentageValue' => 14.0,
			],
			[
				'walletId' => 'wallet_id_2',
				'fixedValue' => 10.0,
			]
		]);
		expect($split->totalPercentage())->toBe(28.0);
		expect($split->totalFixedValue())->toBe(20.0);

		expect(fn() => $split->validateFor(150))->not()->toThrow(InvalidSplitException::class);
	});

	it('throws error if amount to split is invalid', function (): void {
		$split = Split::fromArray([
			[
				'walletId' => 'wallet_id',
				'percentageValue' => 100.0,
			],
			[
				'walletId' => 'wallet_id',
				'percentageValue' => 14.0,
			]
		]);
		expect(fn() => $split->validateFor(10))->toThrow(InvalidSplitException::class, 'Split percentages sum to 114%, which exceeds 100%');

		$split = Split::fromArray([
			[
				'walletId' => 'wallet_id',
				'fixedValue' => 10,
			],
			[
				'walletId' => 'wallet_id',
				'fixedValue' => 14.5,
			]
		]);
		expect(fn() => $split->validateFor(10))->toThrow(InvalidSplitException::class, 'Split fixed values sum to R$ 24.5, which exceeds payment value of R$ 10');
	});
});
