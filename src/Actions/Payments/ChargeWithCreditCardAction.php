<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\DTOs\Payments\ChargeWithCreditCardDTO;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;


final class ChargeWithCreditCardAction extends AbstractAction
{
	/**
	 * Charges a payment with a credit card by its ID.
	 * 
	 * This action performs a pre-request validation to ensure the ID is not
	 * empty and then sends a POST request to the 'payments/{id}/payWithCreditCard' endpoint.
	 *	
	 * @see https://docs.asaas.com/reference/pagar-uma-cobranca-com-cartao-de-credito Official Asaas API Documentation
	 * 
	 * @param  string  $paymentId  The unique identifier of the payment to be charged.
	 * @param  ChargeWithCreditCardDTO  $dto  The data transfer object containing the payment details.
	 * @return array<string, mixed> An array containing the data of the charged payment.
	 * 
	 * @throws \InvalidArgumentException if the provided payment ID is empty.
	 * @throws AuthenticationException
	 * @throws NotFoundException
	 * @throws ValidationException
	 * @throws RateLimitException
	 * @throws ApiException
	 */
	public function handle(string $paymentId, ChargeWithCreditCardDTO $dto): array
	{
		$normalizedId = trim($paymentId);
		if ($normalizedId === '') {
			throw new \InvalidArgumentException('Payment ID cannot be empty');
		}

		return $this->executeRequest(
			fn() => $this->client->post(
				'payments/' . rawurlencode($normalizedId) . '/payWithCreditCard',
				[
					'json' => $dto->toArray(),
				]
			)
		);
	}
}
