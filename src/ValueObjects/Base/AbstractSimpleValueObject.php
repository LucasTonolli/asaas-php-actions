<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Base;

/**
 * Provides a base implementation for simple, string-based Value Objects.
 *
 * This abstract class offers the boilerplate for VOs that encapsulate a single
 * string value. It includes a protected constructor, a getter for the raw value,
 * strict equality comparison, and default serialization methods.
 *
 * Concrete classes extending this should provide their own static `from()`
 * factory method for validation and construction.
 *
 * @property-read string $value The raw, underlying string value.
 */
abstract class AbstractSimpleValueObject
{
    /** @var string The raw, immutable string value. */
    protected readonly string $value;

    /**
     * Protected constructor to enforce immutability and the factory pattern.
     *
     * @internal Should only be called from a static factory method like `from()`.
     *
     * @param  string  $value  The validated string value to encapsulate.
     */
    protected function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Gets the raw, underlying string value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compares this Value Object with another for value equality.
     *
     * The parameter type ensures only instances of this base type are accepted;
     * the runtime check (`$other instanceof static`) restricts equality to the
     * same concrete class.
     *
     * @param  self  $other  The other Value Object to compare with.
     * @return bool True if the values are identical.
     */
    public function equals(self $other): bool
    {
        return $other instanceof static && $this->value === $other->value;
    }
}
