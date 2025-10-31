<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\GetByIdAction;

/**
 * Retrieves the billing information for a specific payment.
 *
 * @see https://docs.asaas.com/reference/recuperar-informacoes-de-pagamento-de-uma-cobranca Official Asaas API Documentation
 */
final class GetPaymentBillingInfoAction extends GetByIdAction
{
    /**
     * {@inheritDoc}
     */
    protected function getResourceName(): string
    {
        return 'Payment';
    }

    /**
     * {@inheritDoc}
     */
    protected function getEndpoint(string $id): string
    {
        return 'payments/'.rawurlencode($id).'/billingInfo';
    }
}
