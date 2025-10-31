<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\RestoreByIdAction;

/**
 * Restores a previously deleted payment.
 *
 * @see https://docs.asaas.com/reference/restaurar-cobranca-removida Official Asaas API Documentation
 */
final class RestorePaymentAction extends RestoreByIdAction
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
        return 'payments/'.rawurlencode($id).'/restore';
    }
}
