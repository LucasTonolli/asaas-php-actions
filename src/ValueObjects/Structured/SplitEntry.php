<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidSplitEntryException;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

final class SplitEntry extends AbstractStructuredValueObject
{
	private function __construct(
		public readonly string $walletId,
		public readonly ?float $fixedValue = null,
		public readonly ?float $percentageValue = null,
		public readonly ?float $totalFixedValue = null,
		public readonly ?string $externalReference = null,
		public readonly ?string $description = null,
	) {}

	public static function create(
		string $walletId,
		?float $fixedValue = null,
		?float $percentageValue = null,
		?float $totalFixedValue = null,
		?string $externalReference = null,
		?string $description = null,
	): self {
		if ($fixedValue === null && $percentageValue === null && $totalFixedValue === null) {
			throw new InvalidSplitEntryException('At least one value must be provided');
		}

		if ($percentageValue !== null && ($percentageValue < 0 || $percentageValue > 100)) {
			throw new InvalidSplitEntryException('Percentual value must be between 0 and 100');
		}

		return new self($walletId, $fixedValue, $percentageValue, $totalFixedValue, $externalReference, $description);
	}

	public static function fromArray(array $data): self
	{
		return self::create(
			walletId: $data['walletId'] ?? throw new InvalidSplitEntryException('walletId is required'),
			fixedValue: $data['fixedValue'] ?? null,
			percentageValue: $data['percentageValue'] ?? null,
			totalFixedValue: $data['totalFixedValue'] ?? null,
			externalReference: $data['externalReference'] ?? null,
			description: $data['description'] ?? null
		);
	}
}
