<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Actions\CreditCard\TokenizationAction;
use AsaasPhpSdk\DTOs\CreditCard\TokenizationDTO;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Exceptions\DTOs\CreditCard\InvalidCreditCardDataException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;

/**
 * Provides a user-friendly interface for tokenizing credit cards.
 *
 * This service acts as the main entry point for tokenizing credit cards in the Asaas API.
 * It abstracts the underlying complexity of DTOs and Actions, providing a clean
 * and simple API for the SDK consumer.
 */
final class CreditCardService
{
    /**
     * @var ResponseHandler The handler for processing API responses.
     *
     * @internal
     */
    private readonly ResponseHandler $responseHandler;

    /**
     * CreditCardService constructor.
     *
     * @param  Client  $client  The configured Guzzle HTTP client.
     * @param  ?ResponseHandler  $responseHandler  Optional custom response handler.
     */
    public function __construct(private Client $client, ?ResponseHandler $responseHandler = null)
    {
        $this->responseHandler = $responseHandler ?? new ResponseHandler;
    }

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

    /**
     * Helper method to create DTOs with consistent error handling.
     *
     * @internal
     *
     * @template T of \AsaasPhpSdk\DTOs\Base\AbstractDTO
     *
     * @param  class-string<T>  $dtoClass  The DTO class to instantiate.
     * @param  array<string, mixed>  $data  The raw data for the DTO.
     * @return T The created DTO instance.
     *
     * @throws ValidationException Wraps internal validation exceptions.
     */
    private function createDTO(string $dtoClass, array $data): \AsaasPhpSdk\DTOs\Base\AbstractDTO
    {
        try {
            return $dtoClass::fromArray($data);
        } catch (InvalidCreditCardDataException $e) {
            throw new ValidationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
