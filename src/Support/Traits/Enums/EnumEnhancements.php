<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Support\Traits\Enums;

trait EnumEnhancements
{
	/**
	 * Safely creates an enum instance from various string representations.
	 *
	 * This method relies on a private static `fromString(string $value): self`
	 * method being implemented in the Enum class that uses this trait.
	 *
	 * @param  string  $value The string representation of the type.
	 * @return static|null The corresponding enum instance or `null` if the value is invalid.
	 */
	public static function tryFromString(string $value): ?static
	{
		try {
			// Assume que um método `fromString` privado e estrito existe na classe que usa o trait
			return self::fromString($value);
		} catch (\ValueError) {
			return null;
		}
	}

	/**
	 * Gets an array containing all possible enum cases defined in the specific Enum.
	 *
	 * @return array<int, static> An array of all enum instances.
	 */
	public static function all(): array
	{
		return self::cases();
	}

	/**
	 * Gets a key-value array of all options, suitable for UI elements like dropdowns.
	 *
	 * This method relies on a public `label(): string` method being implemented
	 * in the Enum class that uses this trait.
	 * The array key is the case name (e.g., 'Boleto') and the value is the
	 * human-readable label (e.g., 'Boleto').
	 *
	 * @return array<string, string> An associative array of options.
	 */
	public static function options(): array
	{
		$options = [];
		/** @var static&EnumEnhancements $enumCase */
		foreach (self::all() as $enumCase) {
			$options[$enumCase->name] = $enumCase->label();
		}
		return $options;
	}
}
