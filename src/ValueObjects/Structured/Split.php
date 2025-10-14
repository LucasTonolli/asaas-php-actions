<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidSplitException;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

/**
 * A Value Object representing a collection of payment split entries.
 *
 * This class acts as a collection that ensures the validity of a group of
 * `SplitEntry` objects. It also contains business logic to validate the entire
 * split configuration against a total payment value.
 */
final class Split extends AbstractStructuredValueObject
{
	/**
	 * @var SplitEntry[] The array of split recipient entries.
	 */
	private array $entries = [];

	/**
	 * Split private constructor.
	 * @internal Forces creation via static factory methods.
	 *
	 * @param  SplitEntry[]  $entries An array of SplitEntry objects.
	 */
	private function __construct(array $entries)
	{
		if (empty($entries)) {
			throw new InvalidSplitException('Split entries must not be empty');
		}

		$this->entries = $entries;
	}

	/**
	 * Creates a new Split instance from an array of pre-validated SplitEntry objects.
	 *
	 * @param  SplitEntry[]  $entries An array of SplitEntry value objects.
	 * @return self A new Split instance.
	 *
	 * @throws InvalidSplitException If the entries array is empty.
	 */
	public static function create(array $entries): self
	{
		return new self($entries);
	}

	/**
	 * Creates a Split instance from a raw, multi-dimensional array of data.
	 *
	 * This factory maps each sub-array into a `SplitEntry` object before creating the collection.
	 *
	 * @param  array<int, array<string, mixed>>  $data The raw array of split entries.
	 * @return self A new Split instance.
	 *
	 * @throws InvalidSplitException If the data array is empty.
	 * @throws InvalidSplitEntryException If any individual entry in the data array is invalid.
	 */
	public static function fromArray(array $data): self
	{
		$entries = array_map(
			fn(array $entry) => SplitEntry::fromArray($entry),
			$data
		);

		return new self($entries);
	}

	/**
	 * Gets the raw array of SplitEntry objects.
	 *
	 * @return SplitEntry[]
	 */
	public function getEntries(): array
	{
		return $this->entries;
	}

	/**
	 * Converts the internal list of SplitEntry objects into an array of arrays.
	 *
	 * Useful for serialization within a DTO's `toArray` method.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function entriesToArray(): array
	{
		return array_map(fn(SplitEntry $entry) => $entry->toArray(), $this->entries);
	}

	/**
	 * Counts the number of entries in the split.
	 * 
	 * @return int
	 */
	public function count(): int
	{
		return count($this->entries);
	}

	/**
	 * Calculates the sum of all percentage-based split entries.
	 * 
	 * @return float
	 */
	public function totalPercentage(): float
	{
		return array_reduce(
			$this->entries,
			fn(float $sum, SplitEntry $entry) => $sum + ($entry->percentageValue ?? 0),
			0
		);
	}

	/**
	 * Calculates the sum of all fixed-value-based split entries.
	 * 
	 * @return float
	 */
	public function totalFixedValue(): float
	{
		return array_reduce(
			$this->entries,
			fn(float $sum, SplitEntry $entry) => $sum + ($entry->totalFixedValue ?? $entry->fixedValue ?? 0),
			0
		);
	}

	/**
	 * Validates the entire split configuration against a total payment value.
	 *
	 * This method checks for business rule violations, such as the total percentage
	 * exceeding 100% or the total fixed value exceeding the payment amount.
	 *
	 * @param  float  $paymentValue The total value of the payment to validate against.
	 *
	 * @throws InvalidSplitException If any of the business rules are violated.
	 */

	public function validateFor(float $paymentValue): void
	{
		$totalPercentage = $this->totalPercentage();
		if ($totalPercentage > 100) {
			throw new InvalidSplitException(
				"Split percentages sum to {$totalPercentage}%, which exceeds 100%"
			);
		}

		$totalFixed = $this->totalFixedValue();
		if ($totalFixed > $paymentValue) {
			throw new InvalidSplitException(
				"Split fixed values sum to R$ {$totalFixed}, which exceeds payment value of R$ {$paymentValue}"
			);
		}
	}
}
