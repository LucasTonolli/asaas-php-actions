<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments\Enums;

use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\Support\Traits\Enums\EnumEnhancements;

/**
 * Defines the possible payment statuses.
 */
enum PaymentStatusEnum: string
{
	use EnumEnhancements;

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
			in_array($normalized, ['cobrança solicitada', 'dunning requested']) => self::DunningRequested,
			in_array($normalized, ['cobrança recebida', 'dunning received']) => self::DunningReceived,
			in_array($normalized, ['aguardando análise de risco', 'awaiting risk analysis']) => self::AwaitingRiskAnalysis,
			default => throw new \ValueError("Invalid payment status '{$value}'"),
		};
	}
}
