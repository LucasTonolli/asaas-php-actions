<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Webhooks;

use AsaasPhpSdk\Actions\Base\GetByIdAction;

/**
 * Retrieves a single webhook by its ID.
 *
 * @see https://docs.asaas.com/reference/recuperar-um-unico-webhook Official Asaas API Documentation
 */
final class GetWebhookAction extends GetByIdAction
{
    /**
     * {@inheritDoc}
     */
    protected function getResourceName(): string
    {
        return 'Webhook';
    }

    /**
     * {@inheritDoc}
     */
    protected function getEndpoint(string $id): string
    {
        return 'webhooks/'.rawurlencode($id);
    }
}
