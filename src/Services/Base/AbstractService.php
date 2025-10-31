<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services\Base;

use AsaasPhpSdk\DTOs\Base\AbstractDTO;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Exceptions\DTOs\Base\InvalidDataException;
use AsaasPhpSdk\Support\Helpers\ResponseHandler;
use GuzzleHttp\Client;

abstract class AbstractService
{
    /**
     * The response handler instance for processing API responses.
     *
     * @internal
     */
    protected readonly ResponseHandler $responseHandler;

    /**
     * AbstractService constructor.
     *
     * @param  Client  $client  The configured Guzzle HTTP client.
     * @param  ?ResponseHandler  $responseHandler  Optional custom response handler.
     */
    public function __construct(
        protected Client $client,
        ?ResponseHandler $responseHandler = null
    ) {
        $this->responseHandler = $responseHandler ?? new ResponseHandler;
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
    protected function createDTO(string $dtoClass, array $data): AbstractDTO
    {
        try {
            return $dtoClass::fromArray($data);
        } catch (InvalidDataException $e) {
            throw new ValidationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
