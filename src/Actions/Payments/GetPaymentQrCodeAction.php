<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class GetPaymentQrCodeAction extends AbstractAction
{
    /**
     *  Retrieves the QR code for a specific payment with PIX, boleto, or Undefined billing type.
     *
     * @see https://docs.asaas.com/reference/obter-qr-code-para-pagamentos-via-pix Official Asaas API Documentation
     *
     * @param  string  $paymentId  The ID of the payment to retrieve the QR code for.
     * @return array<string, mixed> The QR code details including encoded image, payload, expiration date, and description.
     *
     * @throws \InvalidArgumentException if the provided payment ID is empty.
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function handle(string $paymentId): array
    {
        $normalizedId = trim($paymentId);

        if ($normalizedId === '') {
            throw new \InvalidArgumentException('Payment ID cannot be empty');
        }

        return $this->executeRequest(
            fn () => $this->client->get('/payments/'.rawurlencode($normalizedId).'/pixQrCode')
        );
    }
}
