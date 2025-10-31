<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\GetByIdAction;

/**
 * Retrieves a single payment by its ID.
 *
 * @see https://docs.asaas.com/reference/recuperar-uma-unica-cobranca Official Asaas API Documentation
 */
final class GetPaymentAction extends GetByIdAction
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
