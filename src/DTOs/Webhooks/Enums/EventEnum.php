<?php

namespace AsaasPhpSdk\DTOs\Webhooks\Enums;

use AsaasPhpSdk\Support\Helpers\DataSanitizer;
use AsaasPhpSdk\Support\Traits\Enums\EnumEnhancements;

/**
 * Defines the possible event types for webhooks.
 */
enum EventEnum: string
{
    use EnumEnhancements;

    case PaymentAuthorized = 'PAYMENT_AUTHORIZED';
    case PaymentAwaitingRiskAnalysis = 'PAYMENT_AWAITING_RISK_ANALYSIS';
    case PaymentApprovedByRiskAnalysis = 'PAYMENT_APPROVED_BY_RISK_ANALYSIS';
    case PaymentReprovedByRiskAnalysis = 'PAYMENT_REPROVED_BY_RISK_ANALYSIS';
    case PaymentCreated = 'PAYMENT_CREATED';
    case PaymentConfirmed = 'PAYMENT_CONFIRMED';
    case PaymentReceived = 'PAYMENT_RECEIVED';
    case PaymentAnticipated = 'PAYMENT_ANTICIPATED';
    case PaymentOverdue = 'PAYMENT_OVERDUE';
    case PaymentDeleted = 'PAYMENT_DELETED';
    case PaymentRestored = 'PAYMENT_RESTORED';
    case PaymentRefunded = 'PAYMENT_REFUNDED';
    case PaymentRefundInProgress = 'PAYMENT_REFUND_IN_PROGRESS';
    case PaymentRefundDenied = 'PAYMENT_REFUND_DENIED';
    case PaymentReceivedInCashUndone = 'PAYMENT_RECEIVED_IN_CASH_UNDONE';
    case PaymentChargebackRequested = 'PAYMENT_CHARGEBACK_REQUESTED';
    case PaymentChargebackDispute = 'PAYMENT_CHARGEBACK_DISPUTE';
    case PaymentAwaitingChargebackReversal = 'PAYMENT_AWAITING_CHARGEBACK_REVERSAL';
    case PaymentDunningReceived = 'PAYMENT_DUNNING_RECEIVED';
    case PaymentDunningRequested = 'PAYMENT_DUNNING_REQUESTED';
    case PaymentBankSlipViewed = 'PAYMENT_BANK_SLIP_VIEWED';
    case PaymentCheckoutViewed = 'PAYMENT_CHECKOUT_VIEWED';
    case PaymentCreditCardCaptureRefused = 'PAYMENT_CREDIT_CARD_CAPTURE_REFUSED';
    case PaymentPartiallyRefunded = 'PAYMENT_PARTIALLY_REFUNDED';
    case PaymentSplitCancelled = 'PAYMENT_SPLIT_CANCELLED';
    case PaymentSplitDivergenceBlock = 'PAYMENT_SPLIT_DIVERGENCE_BLOCK';
    case PaymentSplitDivergenceBlockFinished = 'PAYMENT_SPLIT_DIVERGENCE_BLOCK_FINISHED';
    case InvoiceCreated = 'INVOICE_CREATED';
    case InvoiceUpdated = 'INVOICE_UPDATED';
    case InvoiceSynchronized = 'INVOICE_SYNCHRONIZED';
    case InvoiceAuthorized = 'INVOICE_AUTHORIZED';
    case InvoiceProcessingCancellation = 'INVOICE_PROCESSING_CANCELLATION';
    case InvoiceCanceled = 'INVOICE_CANCELED';
    case InvoiceCancellationDenied = 'INVOICE_CANCELLATION_DENIED';
    case InvoiceError = 'INVOICE_ERROR';
    case TransferCreated = 'TRANSFER_CREATED';
    case TransferPending = 'TRANSFER_PENDING';
    case TransferInBankProcessing = 'TRANSFER_IN_BANK_PROCESSING';
    case TransferBlocked = 'TRANSFER_BLOCKED';
    case TransferDone = 'TRANSFER_DONE';
    case TransferFailed = 'TRANSFER_FAILED';
    case TransferCancelled = 'TRANSFER_CANCELLED';
    case BillCreated = 'BILL_CREATED';
    case BillPending = 'BILL_PENDING';
    case BillBankProcessing = 'BILL_BANK_PROCESSING';
    case BillPaid = 'BILL_PAID';
    case BillCancelled = 'BILL_CANCELLED';
    case BillFailed = 'BILL_FAILED';
    case BillRefunded = 'BILL_REFUNDED';
    case ReceivableAnticipationCancelled = 'RECEIVABLE_ANTICIPATION_CANCELLED';
    case ReceivableAnticipationScheduled = 'RECEIVABLE_ANTICIPATION_SCHEDULED';
    case ReceivableAnticipationPending = 'RECEIVABLE_ANTICIPATION_PENDING';
    case ReceivableAnticipationCredited = 'RECEIVABLE_ANTICIPATION_CREDITED';
    case ReceivableAnticipationDebited = 'RECEIVABLE_ANTICIPATION_DEBITED';
    case ReceivableAnticipationDenied = 'RECEIVABLE_ANTICIPATION_DENIED';
    case ReceivableAnticipationOverdue = 'RECEIVABLE_ANTICIPATION_OVERDUE';
    case MobilePhoneRechargePending = 'MOBILE_PHONE_RECHARGE_PENDING';
    case MobilePhoneRechargeCancelled = 'MOBILE_PHONE_RECHARGE_CANCELLED';
    case MobilePhoneRechargeConfirmed = 'MOBILE_PHONE_RECHARGE_CONFIRMED';
    case MobilePhoneRechargeRefunded = 'MOBILE_PHONE_RECHARGE_REFUNDED';
    case AccountStatusBankAccountInfoApproved = 'ACCOUNT_STATUS_BANK_ACCOUNT_INFO_APPROVED';
    case AccountStatusBankAccountInfoAwaitingApproval = 'ACCOUNT_STATUS_BANK_ACCOUNT_INFO_AWAITING_APPROVAL';
    case AccountStatusBankAccountInfoPending = 'ACCOUNT_STATUS_BANK_ACCOUNT_INFO_PENDING';
    case AccountStatusBankAccountInfoRejected = 'ACCOUNT_STATUS_BANK_ACCOUNT_INFO_REJECTED';
    case AccountStatusCommercialInfoApproved = 'ACCOUNT_STATUS_COMMERCIAL_INFO_APPROVED';
    case AccountStatusCommercialInfoAwaitingApproval = 'ACCOUNT_STATUS_COMMERCIAL_INFO_AWAITING_APPROVAL';
    case AccountStatusCommercialInfoExpired = 'ACCOUNT_STATUS_COMMERCIAL_INFO_EXPIRED';
    case AccountStatusCommercialInfoExpiringSoon = 'ACCOUNT_STATUS_COMMERCIAL_INFO_EXPIRING_SOON';
    case AccountStatusCommercialInfoPending = 'ACCOUNT_STATUS_COMMERCIAL_INFO_PENDING';
    case AccountStatusCommercialInfoRejected = 'ACCOUNT_STATUS_COMMERCIAL_INFO_REJECTED';
    case AccountStatusDocumentApproved = 'ACCOUNT_STATUS_DOCUMENT_APPROVED';
    case AccountStatusDocumentAwaitingApproval = 'ACCOUNT_STATUS_DOCUMENT_Awaiting_Approval';
    case AccountStatusDocumentPending = 'ACCOUNT_STATUS_DOCUMENT_PENDING';
    case AccountStatusDocumentRejected = 'ACCOUNT_STATUS_DOCUMENT_REJECTED';
    case AccountStatusGeneralApprovalApproved = 'ACCOUNT_STATUS_GENERAL_APPROVAL_APPROVED';
    case AccountStatusGeneralApprovalAwaitingApproval = 'ACCOUNT_STATUS_GENERAL_APPROVAL_AWAITING_APPROVAL';
    case AccountStatusGeneralApprovalPending = 'ACCOUNT_STATUS_GENERAL_APPROVAL_PENDING';
    case AccountStatusGeneralApprovalRejected = 'ACCOUNT_STATUS_GENERAL_APPROVAL_REJECTED';
    case SubscriptionCreated = 'SUBSCRIPTION_CREATED';
    case SubscriptionUpdated = 'SUBSCRIPTION_UPDATED';
    case SubscriptionInactivated = 'SUBSCRIPTION_INACTIVATED';
    case SubscriptionDeleted = 'SUBSCRIPTION_DELETED';
    case SubscriptionSplitDisabled = 'SUBSCRIPTION_SPLIT_DISABLED';
    case SubscriptionSplitDivergenceBlock = 'SUBSCRIPTION_SPLIT_DIVERGENCE_BLOCK';
    case SubscriptionSplitDivergenceBlockFinished = 'SUBSCRIPTION_SPLIT_DIVERGENCE_BLOCK_FINISHED';
    case CheckoutCreated = 'CHECKOUT_CREATED';
    case CheckoutCanceled = 'CHECKOUT_CANCELED';
    case CheckoutExpired = 'CHECKOUT_EXPIRED';
    case CheckoutPaid = 'CHECKOUT_PAID';
    case BalanceValueBlocked = 'BALANCE_VALUE_BLOCKED';
    case BalanceValueUnblocked = 'BALANCE_VALUE_UNBLOCKED';
    case InternalTransferCredit = 'INTERNAL_TRANSFER_CREDIT';
    case InternalTransferDebit = 'INTERNAL_TRANSFER_DEBIT';
    case AccessTokenCreated = 'ACCESS_TOKEN_CREATED';
    case AccessTokenDeleted = 'ACCESS_TOKEN_DELETED';
    case AccessTokenDisabled = 'ACCESS_TOKEN_DISABLED';
    case AccessTokenEnabled = 'ACCESS_TOKEN_ENABLED';
    case AccessTokenExpired = 'ACCESS_TOKEN_EXPIRED';
    case AccessTokenExpiringSoon = 'ACCESS_TOKEN_EXPIRING_SOON';

    /**
     * Gets the human-readable label for the event type.
     *
     * @return string The label (e.g., 'Payment Authorized', 'Invoice Created').
     */
    public function label(): string
    {
        return ucwords(strtolower(str_replace('_', ' ', $this->value)));
    }

    /**
     * Creates an enum instance from a string representation.
     *
     * * @internal This is the strict factory, used by `tryFromString`.
     *
     * * @param  string  $value  The string representation of the type (e.g., 'payment_authorized', 'Payment_Authorized').
     * @return self The corresponding enum instance.
     *
     * * @throws \ValueError If the string does not match any known type.
     */
    private static function fromString(string $value): self
    {
        $normalized = DataSanitizer::sanitizeLowercase($value);

        $enumValue = strtoupper($normalized);

        $case = self::tryFrom($enumValue);

        if ($case === null) {
            throw new \ValueError("Invalid event type '{$value}'");
        }

        return $case;
    }
}
