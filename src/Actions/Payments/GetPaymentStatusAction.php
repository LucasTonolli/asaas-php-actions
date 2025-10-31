<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\GetByIdAction;

/**
 * Retrieves the status of a payment by its ID.
 *
 * @see https://docs.asaas.com/reference/recuperar-status-de-uma-cobranca Official Asaas API Documentation
 */
final class GetPaymentStatusAction extends GetByIdAction
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
        return 'payments/'.rawurlencode($id).'/status';
    }
}
