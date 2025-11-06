<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Webhooks;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\DTOs\Webhooks\CreateWebhookDTO;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class CreateWebhookAction extends AbstractAction
{
	/**
	 * Creates a new webhook.
	 * 
	 * This action sends a POST request to the 'webhooks' endpoint. The data is
	 * encapsulated and validated by the CreateWebhookDTO before being sent.
	 * 
	 * @see https://docs.asaas.com/reference/criar-novo-webhook Official Asaas API Documentation
	 * 
	 * @param  CreateWebhookDTO  $data  A Data Transfer Object containing the validated webhook data.
	 * @return array<string, mixed> An array representing the newly created webhook as returned by the API.
	 * 
	 * @throws AuthenticationException
	 * @throws NotFoundException
	 * @throws ValidationException
	 * @throws RateLimitException
	 * @throws ApiException
	 */
	public function handle(CreateWebhookDTO $data): array
	{
		return $this->executeRequest(
			fn() => $this->client->post('webhooks', ['json' => $data->toArray()])
		);
	}
}
