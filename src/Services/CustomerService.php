<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Actions\Customers\CreateCustomerAction;
use AsaasPhpSdk\Actions\Customers\DeleteCustomerAction;
use AsaasPhpSdk\Actions\Customers\GetCustomerAction;
use AsaasPhpSdk\Actions\Customers\ListCustomersAction;
use AsaasPhpSdk\Actions\Customers\RestoreCustomerAction;
use AsaasPhpSdk\Actions\Customers\UpdateCustomerAction;
use AsaasPhpSdk\DTOs\Customers\CreateCustomerDTO;
use AsaasPhpSdk\DTOs\Customers\ListCustomersDTO;
use AsaasPhpSdk\DTOs\Customers\UpdateCustomerDTO;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Exceptions\DTOs\Customers\InvalidCustomerDataException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;

/**
 * Provides a user-friendly interface for all customer-related operations.
 *
 * This service acts as the main entry point for managing customers in the Asaas API.
 * It abstracts the underlying complexity of DTOs and Actions, providing a clean
 * and simple API for the SDK consumer.
 *
 * @example
 * $asaas = new AsaasPhpSdk\Asaas('YOUR_API_KEY', 'sandbox');
 * $customers = $asaas->customer->list();
 */
final class CustomerService
{
    /**
     * @var ResponseHandler The handler for processing API responses.
     *
     * @internal
     */
    private readonly ResponseHandler $responseHandler;

    /**
     * CustomerService constructor.
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
     * Creates a new customer.
     *
     * @see https://docs.asaas.com/reference/criar-novo-cliente
     *
     * @param  array<string, mixed>  $data  Customer data (e.g., ['name' => 'John Doe', 'cpfCnpj' => '123...']).
     * @return array <string, mixed> An array representing the newly created customer.
     *
     * @throws ValidationException
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function create(array $data): array
    {
        $dto = $this->createDTO(CreateCustomerDTO::class, $data);
        $action = new CreateCustomerAction($this->client, $this->responseHandler);

        return $action->handle($dto);
    }

    /**
     * Retrieves a paginated list of customers.
     *
     * @see https://docs.asaas.com/reference/listar-clientes
     *
     * @param  array<string, mixed>  $filters  Optional filters (e.g., ['name' => 'John', 'limit' => 10]).
     * @return array <string, mixed> A paginated list of customers.
     *
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function list(array $filters = []): array
    {
        $dto = $this->createDTO(ListCustomersDTO::class, $filters);
        $action = new ListCustomersAction($this->client, $this->responseHandler);

        return $action->handle($dto);
    }

    /**
     * Retrieves a single customer by their ID.
     *
     * @see https://docs.asaas.com/reference/recuperar-um-unico-cliente
     *
     * @param  string  $id  The unique identifier of the customer.
     * @return array <string, mixed> An array containing the customer's data.
     *
     * @throws \InvalidArgumentException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function get(string $id): array
    {
        $action = new GetCustomerAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Updates an existing customer by their ID.
     *
     * @see https://docs.asaas.com/reference/atualizar-cliente-existente
     *
     * @param  string  $id  The unique identifier of the customer.
     * @param  array<string, mixed>  $data  The customer data to be updated.
     * @return array <string, mixed> An array representing the updated customer.
     *
     * @throws \InvalidArgumentException
     * @throws ValidationException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function update(string $id, array $data): array
    {
        $dto = $this->createDTO(UpdateCustomerDTO::class, $data);
        $action = new UpdateCustomerAction($this->client, $this->responseHandler);

        return $action->handle($id, $dto);
    }

    /**
     * Deletes a customer by their ID.
     *
     * @see https://docs.asaas.com/reference/remover-cliente
     *
     * @param  string  $id  The unique identifier of the customer.
     * @return array <string, mixed> An array confirming the deletion.
     *
     * @throws \InvalidArgumentException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function delete(string $id): array
    {
        $action = new DeleteCustomerAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Restores a previously deleted customer by their ID.
     *
     * @see https://docs.asaas.com/reference/restaurar-cliente-removido
     *
     * @param  string  $id  The unique identifier of the customer.
     * @return array <string, mixed> An array containing the restored customer's data.
     *
     * @throws \InvalidArgumentException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function restore(string $id): array
    {
        $action = new RestoreCustomerAction($this->client, $this->responseHandler);

        return $action->handle($id);
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
        } catch (InvalidCustomerDataException $e) {
            throw new ValidationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
