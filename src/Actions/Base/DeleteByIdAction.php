<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Base;

use AsaasPhpSdk\Actions\Traits\ValidateResourceIdTrait;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

/**
 * Abstract base class for DELETE actions that delete a resource by ID.
 */
abstract class DeleteByIdAction extends AbstractAction
{
	use ValidateResourceIdTrait;

	/**
	 * Handles the DELETE request for a resource by ID.
	 *
	 * @param  string  $id  The resource ID.
	 * @return array<string, mixed> The deletion response.
	 * 
	 * @throws \InvalidArgumentException if the provided customer ID is empty.
	 * @throws AuthenticationException
	 * @throws NotFoundException
	 * @throws ValidationException
	 * @throws RateLimitException
	 * @throws ApiException	
	 */
	public function handle(string $id): array
	{
		$normalizedId = $this->validateAndNormalizeId($id, $this->getResourceName());
		$endpoint = $this->getEndpoint($normalizedId);

		return $this->executeRequest(
			fn() => $this->client->delete($endpoint)
		);
	}

	/**
	 * Returns the name of the resource for error messages.
	 *
	 * @return string The resource name (e.g., 'Payment', 'Customer').
	 */
	abstract protected function getResourceName(): string;

	/**
	 * Get the endpoint URL for the resource.
	 *
	 * @param  string  $id  The normalized and validated resource ID.
	 * @return string The complete endpoint path.
	 */
	abstract protected function getEndpoint(string $id): string;
}
