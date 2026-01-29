<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services\Base;

use AsaasPhpSdk\DTOs\Base\AbstractDTO;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Exceptions\DTOs\Base\InvalidDataException;
use AsaasPhpSdk\Support\Http\HttpTransporter;
use GuzzleHttp\Client;

/**
 * Base class for all domain services.
 *
 * This class provides common utilities for services, such as access to the 
 * internal transporter and safe DTO instantiation.
 */
abstract class AbstractService
{
    /**
     * AbstractService constructor.
     *
     * @param HttpTransporter $transporter The engine responsible for HTTP communication.
     */
    public function __construct(
        protected HttpTransporter $transporter,
    ) {}

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
