<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\AbstractAction;

final class GetPaymentQrCodeAction extends AbstractAction
{
	public function handle(string $paymentId): array
	{
		$normalizedId = trim($paymentId);

		if ($normalizedId === '') {
			throw new \InvalidArgumentException('Payment ID cannot be empty.');
		}

		return $this->executeRequest(
			fn() => $this->client->get("/payments/" . rawurldecode($normalizedId) . "/pixQrCode")
		);
	}
}
