<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Base;

use ReflectionClass;

/**
 * Provides a base implementation for structured, composite Value Objects.
 *
 * This class offers boilerplate for VOs composed of multiple properties,
 * including other Value Objects. It provides a generic, recursive `toArray`
 * method and a pragmatic `equals` method based on array comparison.
 *
 * @internal This is an internal framework class and should not be used directly.
 */
abstract readonly class AbstractStructuredValueObject
{
    /**
     * Recursively converts the structured value object into an associative array.
     *
     * This method uses Reflection to access all properties (public, protected,
     * and private). It intelligently serializes nested objects:
     * - Calls `toArray()` on other structured VOs.
     * - Calls `toArray()` on each element in an array of structured VOs.
     * - Calls `value()` on simple, string-based VOs.
     * - Uses the primitive value for all other types.
     *
     * Properties with `null` values are excluded from the final array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();
        $result = [];

        foreach ($properties as $property) {

            if (! $property->isInitialized($this)) {
                continue;
            }

            $key = $property->getName();
            $value = $property->getValue($this);

            if ($value === null) {
                continue;
            }

            if ($value instanceof self) {
                $result[$key] = $value->toArray();
            } elseif (is_array($value) && ! empty($value) && current($value) instanceof self) {
                $result[$key] = array_map(fn(self $v) => $v->toArray(), $value);
            } elseif (is_object($value) && method_exists($value, 'value')) {
                $result[$key] = $value->value();
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Performs a value-based equality comparison by comparing array representations.
     *
     * This method provides a generic equality check by converting both this object
     * and the other object to arrays and then performing a strict comparison.
     * This ensures equality is based on the contained values, not object identity.
     *
     * @param  self  $other  The other object to compare with.
     * @return bool True if the array representations are identical.
     */
    public function equals(self $other): bool
    {
        return $this->toArray() === $other->toArray();
    }
}
