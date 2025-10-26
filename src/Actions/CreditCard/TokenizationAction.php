<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\CreditCard;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\DTOs\CreditCard\TokenizationDTO;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class TokenizationAction extends AbstractAction
{
	/**
	 * Tokenize a credit card.
	 * 
	 * This action sends a POST request to the Asaas API to tokenize a credit card.
	 * 
	 * @see https://docs.asaas.com/reference/tokenizacao-de-cartao-de-credito Official Asaas API Documentation
	 * 
	 * @param  TokenizationDTO  $dto  The data transfer object containing the credit card details.
	 * @return array<string, mixed> An array containing the tokenized credit card data.
	 * 
	 * @throws AuthenticationException
	 * @throws ValidationException
	 * @throws RateLimitException
	 * @throws ApiException
	 */
	public function handle(TokenizationDTO $dto): array
	{
		return $this->executeRequest(
			fn() => $this->client->post('creditCard/tokenizeCreditCard', ['json' => $dto->toArray()])
		);
	}
}
