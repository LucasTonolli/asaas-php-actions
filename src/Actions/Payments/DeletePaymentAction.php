<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\DeleteByIdAction;

/**
 * Deletes a payment by its ID.
 *
 * @see https://docs.asaas.com/reference/excluir-cobranca Official Asaas API Documentation
 */
final class DeletePaymentAction extends DeleteByIdAction
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
        return 'payments/'.rawurlencode($id);
    }
}
