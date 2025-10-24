<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\DTOs\Payments\ChargeWithCreditCardDTO;

final class ChargeWithCreditCardAction extends AbstractAction
{
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
