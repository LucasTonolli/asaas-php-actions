<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Actions\CreditCard\TokenizationAction;
use AsaasPhpSdk\DTOs\CreditCard\TokenizationDTO;
use AsaasPhpSdk\Services\Base\AbstractService;

/**
 * Provides a user-friendly interface for tokenizing credit cards.
 *
 * This service acts as the main entry point for tokenizing credit cards in the Asaas API.
 * It abstracts the underlying complexity of DTOs and Actions, providing a clean
 * and simple API for the SDK consumer.
 */
final class CreditCardService extends AbstractService
{
    /**
     * Tokenizes a credit card.
     *
     * @see https://docs.asaas.com/reference/tokenizacao-de-cartao-de-credito
     *
     * @param  array<string, mixed>  $data  customer id, credit card data, credit card holder data and remote IP.
     * @return array<string, mixed> An array containing the tokenized credit card data.
     */
    public function tokenize(array $data): array
    {
        $dto = $this->createDTO(TokenizationDTO::class, $data);
        $action = new TokenizationAction($this->client, $this->responseHandler);

        return $action->handle($dto);
    }
}
