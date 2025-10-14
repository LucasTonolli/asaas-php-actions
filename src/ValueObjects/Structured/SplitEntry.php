<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidSplitEntryException;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

/**
 * A Value Object representing a single recipient entry in a payment split.
 *
 * This class encapsulates the data for a single destination in a split transaction,
 * including the recipient's wallet ID and the value they will receive (either
 * fixed or as a percentage).
 */
final class SplitEntry extends AbstractStructuredValueObject
{
    /**
     * SplitEntry private constructor.
     *
     * @internal Forces creation via static factory methods.
     *
     * @param  string  $walletId  The unique ID of the Asaas wallet that will receive the funds.
     * @param  ?float  $fixedValue  The fixed monetary amount to be sent.
     * @param  ?float  $percentageValue  The percentage of the total value to be sent.
     * @param  ?float  $totalFixedValue  The fixed amount to be sent from the total transaction value.
     * @param  ?string  $externalReference  A custom external identifier.
     * @param  ?string  $description  A custom description for this split entry.
     */
    private function __construct(
        public readonly string $walletId,
        public readonly ?float $fixedValue = null,
        public readonly ?float $percentageValue = null,
        public readonly ?float $totalFixedValue = null,
        public readonly ?string $externalReference = null,
        public readonly ?string $description = null,
    ) {}

    /**
     * Creates a new SplitEntry instance with explicit, validated parameters.
     *
     * This is the primary factory, performing all core business logic validations.
     *
     * @param  string  $walletId  The recipient's wallet ID.
     * @param  ?float  $fixedValue  A fixed amount.
     * @param  ?float  $percentageValue  A percentage amount (0-100).
     * @param  ?float  $totalFixedValue  A fixed amount from the total value.
     * @param  ?string  $externalReference  A custom external identifier.
     * @param  ?string  $description  A custom description.
     * @return self A new, validated SplitEntry instance.
     *
     * @throws InvalidSplitEntryException If validation fails (e.g., no value provided, or percentage is invalid).
     */
    public static function create(
        string $walletId,
        ?float $fixedValue = null,
        ?float $percentageValue = null,
        ?float $totalFixedValue = null,
        ?string $externalReference = null,
        ?string $description = null,
    ): self {
        $walletId = trim($walletId);
        if ($walletId === '') {
            throw new InvalidSplitEntryException('walletId must be a non-empty string');
        }
        if ($fixedValue === null && $percentageValue === null && $totalFixedValue === null) {
            throw new InvalidSplitEntryException('At least one value must be provided');
        }

        if ($percentageValue !== null && ($percentageValue < 0 || $percentageValue > 100)) {
            throw new InvalidSplitEntryException('Percentage value must be between 0 and 100');
        }

        return new self($walletId, $fixedValue, $percentageValue, $totalFixedValue, $externalReference, $description);
    }

    /**
     * Creates a SplitEntry instance from a raw data array.
     *
     * @param  array{
     * walletId?: string,
     * fixedValue?: float,
     * percentageValue?: float,
     * totalFixedValue?: float,
     * externalReference?: string,
     * description?: string
     * }  $data The raw data array.
     * @return self A new, validated SplitEntry instance.
     *
     * @throws InvalidSplitEntryException If the required 'walletId' key is missing.
     */
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
