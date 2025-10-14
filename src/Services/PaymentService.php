<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Actions\Payments\CreatePaymentAction;
use AsaasPhpSdk\DTOs\Payments\CreatePaymentDTO;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;

/**
 * Provides a user-friendly interface for all payment-related operations.
 *
 * This service acts as the main entry point for managing payments in the Asaas API.
 * It abstracts the underlying complexity of DTOs and Actions, providing a clean
 * and simple API for the SDK consumer.
 *
 * @example
 * $asaas = new AsaasPhpSdk\AsaasClient('YOUR_API_KEY', isSandbox: true);
 * $paymentData = [
 * 'customer' => 'cus_000000000001',
 * 'billingType' => 'BOLETO',
 * 'value' => 100.50,
 * 'dueDate' => '2025-12-31',
 * ];
 * $newPayment = $asaas->payment()->create($paymentData);
 */
final class PaymentService
{
    /**
     * @var ResponseHandler The handler for processing API responses.
     *
     * @internal
     */
    private readonly ResponseHandler $responseHandler;

    /**
     * PaymentService constructor.
     *
     * @param  Client  $client  The configured Guzzle HTTP client.
     * @param  ?ResponseHandler  $responseHandler  Optional custom response handler.
     *
     * @internal
     */
    public function __construct(private Client $client, ?ResponseHandler $responseHandler = null)
    {
        $this->responseHandler = $responseHandler ?? new ResponseHandler;
    }

    /**
     * Creates a new payment.
     *
     * @see https://docs.asaas.com/reference/criar-nova-cobranca
     *
     * @param  array<string, mixed>  $data  Payment data (e.g., ['customer' => '...', 'billingType' => '...', 'value' => ...]).
     * @return array An array representing the newly created payment.
     *
     * @throws ValidationException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function create(array $data): array
    {
        $dto = $this->createDTO(CreatePaymentDTO::class, $data);
        $action = new CreatePaymentAction($this->client, $this->responseHandler);

        return $action->handle($dto);
    }

    /**
     * Helper method to create DTOs with consistent error handling.
     *
     * @internal
     *
     * @template T of AbstractDTO
     * @template T of \AsaasPhpSdk\DTOs\Base\AbstractDTO
     *
     * @param  class-string<T>  $dtoClass  The DTO class to instantiate.
     * @param  array<string, mixed>  $data  The raw data for the DTO.
     * @return T The created DTO instance.
     *
     * @throws ValidationException Wraps internal DTO validation exceptions.
     */
    private function createDTO(string $dtoClass, array $data): \AsaasPhpSdk\DTOs\Base\AbstractDTO
    {
        try {
            return $dtoClass::fromArray($data);
        } catch (InvalidPaymentDataException $e) {
            throw new ValidationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
