<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured\Enums;

use AsaasPhpSdk\Helpers\DataSanitizer;

enum FineType: string
{
	case Fixed = 'FIXED';
	case Percentage = 'PERCENTAGE';

	public function label(): string
	{
		return match ($this) {
			self::Fixed => 'Fixo',
			self::Percentage => 'Porcentagem',
		};
	}

	private static function fromString(string $value): self
	{
		$normalized = DataSanitizer::sanitizeLowercase($value);

		return match (true) {
			in_array($normalized, ['fixed', 'fixo']) => self::Fixed,
			in_array($normalized, ['percentage', 'porcentagem']) => self::Percentage,
			default => throw new \ValueError("Invalid fine type '{$value}'"),
		};
	}

	public static function tryFromString(string $value): ?self
	{
		try {
			return self::fromString($value);
		} catch (\ValueError) {
			return null;
		}
	}

	public static function all(): array
	{
		return [
			self::Fixed,
			self::Percentage,
		];
	}

	public static function options(): array
	{
		$options = [];

		foreach (self::all() as $fineType) {
			$options[$fineType->name] = $fineType->label();
		}

		return $options;
	}
}
