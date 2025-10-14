<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Base;

use AsaasPhpSdk\DTOs\Attributes\ToArrayMethodAttribute;
use AsaasPhpSdk\DTOs\Contracts\DTOContract;
use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;
use AsaasPhpSdk\Helpers\DataSanitizer;

/**
 * Base class for all Data Transfer Objects (DTOs).
 *
 * Provides shared functionality for all DTOs, including a dynamic `toArray`
 * method and a suite of helper methods for sanitization and validation.
 * It also enforces that any concrete DTO must implement its own `sanitize` method.
 *
 * @internal This is an internal framework class and should not be used directly.
 */
abstract class AbstractDTO implements DTOContract
{
    /**
     * Converts the DTO's public properties to an associative array.
     *
     * This method uses Reflection to dynamically build an array. It intelligently
     * serializes objects:
     * - Obeys a `#[ToArrayMethodAttribute]` if present.
     * - Converts Backed Enums to their scalar value (e.g., 'CREDIT_CARD').
     * - Converts Pure Enums to their case name (e.g., 'Boleto').
     * - Calls `->value()` on simple Value Objects.
     * - Excludes properties with `null` values from the output.
     *
     * @return array<string, mixed> The DTO's data as an array.
     */
    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $result = [];

        foreach ($properties as $property) {
            $key = $property->getName();
            $value = $property->getValue($this);

            if ($value === null) {
                continue;
            }

            $attributes = $property->getAttributes(ToArrayMethodAttribute::class);
            if (! empty($attributes)) {
                $attr = $attributes[0]->newInstance();
                $method = $attr->method;
                $args = $attr->args ?? [];
                $result[$key] = $value->{$method}(...$args);
            } elseif ($value instanceof \BackedEnum) {
                $result[$key] = $value->value;
            } elseif ($value instanceof \UnitEnum) {
                $result[$key] = $value->name;
            } elseif (is_object($value) && method_exists($value, 'value')) {
                $result[$key] = $value->value();
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Validates and instantiates a simple Value Object (that uses `::from()`).
     *
     * @param  array<string, mixed>  &$data  The data array, passed by reference.
     * @param  string  $key  The key in the data array to validate.
     * @param  class-string  $valueObjectClass  The fully qualified class name of the Value Object.
     *
     * @throws InvalidValueObjectException if the value is invalid and the VO cannot be created.
     */
    protected static function validateSimpleValueObject(array &$data, string $key, string $valueObjectClass): void
    {
        if (! isset($data[$key])) {
            return;
        }

        try {
            $data[$key] = $valueObjectClass::from($data[$key]);
        } catch (\Exception $e) {
            throw new InvalidValueObjectException(
                "Invalid format for '{$key}': ".$e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Validates and instantiates a structured Value Object (that uses `::fromArray()`).
     *
     * @param  array<string, mixed>  &$data  The data array, passed by reference.
     * @param  string  $key  The key in the data array to validate.
     * @param  class-string  $voClass  The fully qualified class name of the structured VO.
     *
     * @throws InvalidValueObjectException if the value is invalid and the VO cannot be created.
     */
    protected static function validateStructuredValueObject(array &$data, string $key, string $voClass): void
    {
        if (isset($data[$key]) && is_array($data[$key])) {
            try {
                $data[$key] = $voClass::fromArray($data[$key]);
            } catch (\Throwable $e) {
                throw new InvalidValueObjectException(
                    "Invalid format for '{$key}': ".$e->getMessage(),
                    0,
                    $e
                );
            }
        }
    }

    /**
     * Sanitizes an optional string value from the data array.
     *
     * @param  array<string, mixed>  $data  The source data array.
     * @param  string  $key  The key to look for.
     * @return ?string The sanitized string, or null if the key doesn't exist or the value is empty.
     */
    protected static function optionalString(array $data, string $key): ?string
    {
        return array_key_exists($key, $data)
            ? DataSanitizer::sanitizeString($data[$key])
            : null;
    }

    /**
     * Sanitizes an optional boolean value from the data array.
     *
     * @param  array<string, mixed>  $data  The source data array.
     * @param  string  $key  The key to look for.
     * @return ?bool The sanitized boolean, or null if the key doesn't exist or the value is empty.
     */
    protected static function optionalBoolean(array $data, string $key): ?bool
    {
        return array_key_exists($key, $data)
            ? DataSanitizer::sanitizeBoolean($data[$key])
            : null;
    }

    /**
     * Sanitizes an optional string value from the data array, keeping only digits.
     *
     * @param  array<string, mixed>  $data  The source data array.
     * @param  string  $key  The key to look for.
     * @return ?string The sanitized string of digits, or null if the key doesn't exist or the value is empty.
     */
    protected static function optionalOnlyDigits(array $data, string $key): ?string
    {
        if (! array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
            return null;
        }

        return DataSanitizer::onlyDigits($data[$key]);
    }

    /**
     * Sanitizes an optional integer value from the data array.
     *
     * @param  array<string, mixed>  $data  The source data array.
     * @param  string  $key  The key to look for.
     * @return ?int The sanitized integer, or null if the key doesn't exist or the value is empty.
     */
    protected static function optionalInteger(array $data, string $key): ?int
    {
        if (! array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
            return null;
        }

        return DataSanitizer::sanitizeInteger($data[$key]);
    }

    /**
     * Sanitizes an optional float value from the data array.
     *
     * @param  array<string, mixed>  $data  The source data array.
     * @param  string  $key  The key to look for.
     * @return ?float The sanitized float, or null if the key doesn't exist or the value is empty.
     */
    protected static function optionalFloat(array $data, string $key): ?float
    {
        if (! array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
            return null;
        }

        return DataSanitizer::sanitizeFloat($data[$key]);
    }

    /**
     * Defines the contract for sanitizing the DTO's raw input data.
     *
     * Each concrete DTO class must implement this method to clean and normalize
     * its specific set of properties before any validation occurs.
     *
     * @param  array<string, mixed>  $data  The raw input data.
     * @return array<string, mixed> The sanitized data array.
     */
    abstract protected static function sanitize(array $data): array;
}
