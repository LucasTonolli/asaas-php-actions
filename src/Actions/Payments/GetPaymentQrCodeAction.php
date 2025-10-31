<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\GetByIdAction;

/**
 *  Retrieves the QR code for a specific payment with PIX, boleto, or Undefined billing type.
 *
 * @see https://docs.asaas.com/reference/obter-qr-code-para-pagamentos-via-pix Official Asaas API Documentation
 */
final class GetPaymentQrCodeAction extends GetByIdAction
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
        return 'payments/'.rawurlencode($id).'/pixQrCode';
    }
}
