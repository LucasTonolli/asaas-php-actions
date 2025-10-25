<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Actions\Payments\ChargeWithCreditCardAction;
use AsaasPhpSdk\Actions\Payments\CreatePaymentAction;
use AsaasPhpSdk\Actions\Payments\DeletePaymentAction;
use AsaasPhpSdk\Actions\Payments\GetPaymentAction;
use AsaasPhpSdk\Actions\Payments\GetPaymentQrCodeAction;
use AsaasPhpSdk\Actions\Payments\GetPaymentStatusAction;
use AsaasPhpSdk\Actions\Payments\GetPaymentTicketLineAction;
use AsaasPhpSdk\Actions\Payments\ListPaymentsAction;
use AsaasPhpSdk\Actions\Payments\RestorePaymentAction;
use AsaasPhpSdk\DTOs\Payments\ChargeWithCreditCardDTO;
use AsaasPhpSdk\DTOs\Payments\CreatePaymentDTO;
use AsaasPhpSdk\DTOs\Payments\ListPaymentsDTO;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\Exceptions\InvalidDateRangeException;
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
     * @return array<string, mixed> An array representing the newly created payment.
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
     * List payments.
     *
     * @see https://docs.asaas.com/reference/listar-cobrancas
     *
     * @param  array<string, mixed>  $filters  Optional filters (e.g., ['installment' => 'xxxxx', 'limit' => 10]).
     * @return array<string, mixed> A paginated list of payments.
     *
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function list(array $filters = []): array
    {
        $dto = $this->createDTO(ListPaymentsDTO::class, $filters);
        $action = new ListPaymentsAction($this->client, $this->responseHandler);

        return $action->handle($dto);
    }

    /**
     * Retrieves a specific payment by its ID.
     *
     * @see https://docs.asaas.com/reference/recuperar-uma-unica-cobranca
     *
     * @param  string  $id  The ID of the payment to retrieve.
     * @return array<string, mixed> An array containing the data of the specified payment.
     *
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ApiException
     * @throws ValidationException
     */
    public function get(string $id): array
    {
        $action = new GetPaymentAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Deletes a payment by its ID.
     *
     * @see https://docs.asaas.com/reference/excluir-cobranca
     *
     * @param  string  $id  The ID of the payment to delete.
     * @return array<string, mixed> An array confirming the deletion, typically containing a 'deleted' flag.
     *
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ApiException
     * @throws ValidationException
     * @throws \InvalidArgumentException if the provided ID is empty.
     */
    public function delete(string $id): array
    {
        $action = new DeletePaymentAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Restores a deleted payment by its ID.
     *
     * @see https://docs.asaas.com/reference/restaurar-cobranca-removida
     *
     * @param  string  $id  The ID of the payment to restore.
     * @return array<string, mixed> An array containing the restored payment's data.
     *
     * @throws \InvalidArgumentException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function restore(string $id): array
    {
        $action = new RestorePaymentAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Retrieves the status of a specific payment by its ID.
     *
     * @see https://docs.asaas.com/reference/recuperar-status-de-uma-cobranca
     *
     * @param  string  $id  The ID of the payment whose status is to be retrieved.
     * @return array<string, mixed> An array containing the status of the specified payment.
     *
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ApiException
     * @throws ValidationException
     * @throws \InvalidArgumentException if the provided ID is empty.
     */
    public function getStatus(string $id): array
    {
        $action = new GetPaymentStatusAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Retrieves the ticket line information for a boleto or undefined payment by its ID.
     *
     * @see https://docs.asaas.com/reference/obter-linha-digitavel-do-boleto
     *
     * @param  string  $id  The ID of the payment whose ticket line is to be retrieved.
     * @return array<string, mixed> An array containing the ticket line information, including 'identificationField', 'nossoNumero', and 'barcode'.
     *
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ApiException
     * @throws ValidationException
     * @throws \InvalidArgumentException if the provided ID is empty.
     */
    public function getTicketLine(string $id): array
    {
        $action = new GetPaymentTicketLineAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Retrieves the QR code for a Pix payment by its ID.
     *
     * @see https://docs.asaas.com/reference/obter-qr-code-para-pagamentos-via-pix
     *
     * @param  string  $id  The ID of the payment whose QR code is to be retrieved.
     * @return array<string, mixed> An array containing the QR code information, including 'encodedImage', 'payload', 'expirationDate', and 'description'.
     *
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ApiException
     * @throws ValidationException
     * @throws \InvalidArgumentException if the provided ID is empty.
     */
    public function getQrCode(string $id): array
    {
        $action = new GetPaymentQrCodeAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Charges a payment with a credit card.
     * 
     * @see https://docs.asaas.com/reference/cobrar-com-cartao-de-credito
     * 
     * @param  string  $id  The ID of the payment to be charged.
     * @param  array<string, mixed>  $data  raw data.
     * @return array<string, mixed> An array containing the data of the charged payment.
     * 
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ApiException
     * @throws ValidationException
     * @throws \InvalidArgumentException if the provided ID is empty.
     */
    public function chargeWithCreditCard(string $id, array $data): array
    {
        $dto = $this->createDTO(ChargeWithCreditCardDTO::class, $data);
        $action = new ChargeWithCreditCardAction($this->client, $this->responseHandler);

        return $action->handle($id, $dto);
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
     * @throws ValidationException Wraps internal DTO validation exceptions.
     */
    private function createDTO(string $dtoClass, array $data): \AsaasPhpSdk\DTOs\Base\AbstractDTO
    {
        try {
            return $dtoClass::fromArray($data);
        } catch (InvalidDateRangeException | InvalidPaymentDataException $e) {
            throw new ValidationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
