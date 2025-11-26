<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Actions\Webhooks\CreateWebhookAction;
use AsaasPhpSdk\Actions\Webhooks\GetWebhookAction;
use AsaasPhpSdk\DTOs\Webhooks\CreateWebhookDTO;
use AsaasPhpSdk\Services\Base\AbstractService;

/**
 * Provides a user-friendly interface for creating webhooks.
 *
 * This service acts as the main entry point for creating webhooks in the Asaas API.
 * It abstracts the underlying complexity of DTOs and Actions, providing a clean
 * and simple API for the SDK consumer.
 */
final class WebhookService extends AbstractService
{
    /**
     * Creates a new webhook.
     *
     * @see https://docs.asaas.com/reference/criar-novo-webhook
     *
     * @param  array<string, mixed>  $data  webhook data.
     * @return array<string, mixed> An array representing the newly created webhook as returned by the API.
     */
    public function create(array $data): array
    {
        $dto = $this->createDTO(CreateWebhookDTO::class, $data);
        $action = new CreateWebhookAction($this->client, $this->responseHandler);

        return $action->handle($dto);
    }

    public function get(string $id): array
    {
        $action = new GetWebhookAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }
}
