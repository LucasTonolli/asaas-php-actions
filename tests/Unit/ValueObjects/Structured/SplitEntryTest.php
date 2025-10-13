<?php

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidSplitEntryException;
use AsaasPhpSdk\ValueObjects\Structured\SplitEntry;

describe('Split Entry Value Object', function (): void {
	it('can be created with a valid split entry', function (): void {
		$splitEntry = SplitEntry::fromArray([
			'walletId' => 'wallet_id',
			'fixedValue' => 10.0,
		]);
		expect($splitEntry->fixedValue)->toBe(10.0)
			->and($splitEntry->percentageValue)->toBeNull()
			->and($splitEntry->totalFixedValue)->toBeNull()
			->and($splitEntry->externalReference)->toBeNull()
			->and($splitEntry->description)->toBeNull();

		$splitEntry = SplitEntry::create(
			walletId: 'wallet_id',
			percentageValue: 25.0,
			description: 'Test description'
		);
		expect($splitEntry->percentageValue)->toBe(25.0);
		expect($splitEntry->description)->toBe('Test description');
		expect($splitEntry->totalFixedValue)->toBeNull()->and($splitEntry->externalReference)->toBeNull()
			->and($splitEntry->fixedValue)->toBeNull();
	});

	it('cannot be created with an invalid split entry', function (): void {
		expect(fn() => SplitEntry::fromArray([
			'walletId' => 'wallet_id'
		]))->toThrow(InvalidSplitEntryException::class, 'At least one value must be provided');

		expect(fn() => SplitEntry::create(
			walletId: 'wallet_id',
			percentageValue: 101.0,
			description: 'Test description'
		))->toThrow(InvalidSplitEntryException::class, 'Percentual value must be between 0 and 100');
	});

	it('walletId is required', function (): void {
		expect(fn() => SplitEntry::fromArray([]))->toThrow(InvalidSplitEntryException::class, 'walletId is required');
	});

	it('compares the same split entry', function (): void {
		$splitEntry1 = SplitEntry::create(
			walletId: 'wallet_id',
			percentageValue: 25.0,
			description: 'Test description'
		);
		$splitEntry2 = SplitEntry::create(
			walletId: 'wallet_id',
			percentageValue: 25.0,
			description: 'Test description'
		);
		$splitEntry3 = SplitEntry::create(
			walletId: 'wallet_id2',
			percentageValue: 25.0,
			description: 'Test description'
		);
		expect($splitEntry1->equals($splitEntry2))->toBeTrue();
		expect($splitEntry1->equals($splitEntry3))->toBeFalse();
	});
});
