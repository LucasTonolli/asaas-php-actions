<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments\Enums;

use AsaasPhpSdk\Helpers\DataSanitizer;

/**
 * Defines the possible payment statuses.
 */
enum PaymentStatusEnum: string
{
	case Pending = 'PENDING';
	case Received = 'RECEIVED';
	case Confirmed = 'CONFIRMED';
	case Overdue = 'OVERDUE';
	case Refunded = 'REFUNDED';
	case ReceivedInCash = 'RECEIVED_IN_CASH';
	case RefundRequested = 'REFUND_REQUESTED';
	case RefundInProgress = 'REFUND_IN_PROGRESS';
	case ChargebackRequested = 'CHARGEBACK_REQUESTED';
	case ChargebackDispute = 'CHARGEBACK_DISPUTE';
	case AwaitingChargebackReversal = 'AWAITING_CHARGEBACK_REVERSAL';
	case DunningRequested = 'DUNNING_REQUESTED';
	case DunningReceived = 'DUNNING_RECEIVED';
	case AwaitingRiskAnalysis = 'AWAITING_RISK_ANALYSIS';

	/**
	 * Gets the human-readable label for the payment status.
	 * 
	 * @return string The label in Portuguese (e.g., 'Pendente', 'Recebido').
	 */
	public function label(): string
	{
		return match ($this) {
			self::Pending => 'Pendente',
			self::Received => 'Recebido',
			self::Confirmed => 'Confirmado',
			self::Overdue => 'Atrasado',
			self::Refunded => 'Reembolsado',
			self::ReceivedInCash => 'Recebido em dinheiro',
			self::RefundRequested => 'Reembolso solicitado',
			self::RefundInProgress => 'Reembolso em andamento',
			self::ChargebackRequested => 'Chargeback solicitado',
			self::ChargebackDispute => 'Chargeback em disputa',
			self::AwaitingChargebackReversal => 'Aguardando reversão de chargeback',
			self::DunningRequested => 'Cobrança solicitada',
			self::DunningReceived => 'Cobrança recebida',
			self::AwaitingRiskAnalysis => 'Aguardando análise de risco',
		};
	}

	private static function fromString(string $value): self
	{
		$normalized = DataSanitizer::sanitizeLowercase($value);

		return match (true) {
			in_array($normalized, ['pendente', 'pending']) => self::Pending,
			in_array($normalized, ['recebido', 'received']) => self::Received,
			in_array($normalized, ['confirmado', 'confirmed']) => self::Confirmed,
			in_array($normalized, ['atrasado', 'overdue']) => self::Overdue,
			in_array($normalized, ['reembolsado', 'refunded']) => self::Refunded,
			in_array($normalized, ['recebido em dinheiro', 'received in cash']) => self::ReceivedInCash,
			in_array($normalized, ['reembolso solicitado', 'refund requested']) => self::RefundRequested,
			in_array($normalized, ['reembolso em andamento', 'refund in progress']) => self::RefundInProgress,
			in_array($normalized, ['chargeback solicitado', 'chargeback requested']) => self::ChargebackRequested,
			in_array($normalized, ['chargeback em disputa', 'chargeback dispute']) => self::ChargebackDispute,
			in_array($normalized, ['aguardando reversão de chargeback', 'awaiting chargeback reversal']) => self::AwaitingChargebackReversal,
			in_array($normalized, ['cobranca solicitada', 'dunning requested']) => self::DunningRequested,
			in_array($normalized, ['cobranca recebida', 'dunning received']) => self::DunningReceived,
			in_array($normalized, ['aguardando análise de risco', 'awaiting risk analysis']) => self::AwaitingRiskAnalysis,
			default => throw new \ValueError("Invalid payment status '{$value}'"),
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
