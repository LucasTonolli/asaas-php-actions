<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments\Enums;

use AsaasPhpSdk\Helpers\DataSanitizer;

/**
 * Defines the possible invoice statuses.
 */
enum InvoiceStatusEnum: string
{
	case Scheduled = 'SCHEDULED';
	case Authorized = 'AUTHORIZED';
	case ProcessingCancellation = 'PROCESSING_CANCELLATION';
	case CancellationDenied = 'CANCELLATION_DENIED';
	case Error = 'ERROR';

	/**
	 * Gets the human-readable label for the invoice status.
	 * 
	 * @return string The label in Portuguese (e.g., 'Agendado', 'Autorizado').
	 */
	public function label(): string
	{
		return match ($this) {
			self::Scheduled => 'Agendado',
			self::Authorized => 'Autorizado',
			self::ProcessingCancellation => 'Processando cancelamento',
			self::CancellationDenied => 'Cancelamento negado',
			self::Error => 'Erro',
		};
	}

	private static function fromString(string $value): self
	{
		$normalized = DataSanitizer::sanitizeLowercase($value);

		return match (true) {
			in_array($normalized, ['agendado', 'scheduled']) => self::Scheduled,
			in_array($normalized, ['autorizado', 'authorized']) => self::Authorized,
			in_array($normalized, ['processando cancelamento', 'processing cancellation']) => self::ProcessingCancellation,
			in_array($normalized, ['cancelamento negado', 'cancellation denied']) => self::CancellationDenied,
			in_array($normalized, ['erro', 'error']) => self::Error,
			default => throw new \ValueError("Invalid invoice status '{$value}'"),
		};
	}

	/**
	 * Safely creates an enum instance from a string representation.
	 * 
	 * This is a lenient factory that accepts multiple aliases. If the string
	 * is not valid, it returns `null` instead of throwing an exception.
	 * 
	 * @param  string  $value  The string representation of the type.
	 * @return self|null The corresponding enum instance or `null` if the value is invalid.
	 */
	public static function tryFromString(string $value): ?self
	{
		try {
			return self::fromString($value);
		} catch (\ValueError) {
			return null;
		}
	}

	/**
	 * Gets an array containing all possible enum cases.
	 * 
	 * @return array<int, self> An array of all enum instances.
	 */
	public static function all(): array
	{
		return self::cases();
	}
}
