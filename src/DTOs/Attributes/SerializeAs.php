<?php

namespace AsaasPhpSdk\DTOs\Attributes;

use Attribute;

/**
 * A PHP Attribute to specify a custom method for array conversion in DTOs.
 *
 * When a DTO's `toArray()` method is called, it usually serializes object
 * properties by calling a default method (like `->value()`). This attribute
 * allows you to override that behavior, specifying a different method and even
 * passing arguments to it.
 *
 * @example
 * final class CreatePaymentDTO extends AbstractDTO
 * {
 * // When this DTO's toArray() is called, it will execute:
 * // $this->dueDate->format('Y-m-d')
 *
 * #[ToArrayMethodAttribute(method: 'format', args: ['Y-m-d'])]
 * public readonly \DateTimeImmutable $dueDate;
 * }
 */
#[Attribute(\Attribute::TARGET_PROPERTY)]
final class SerializeAs
{
    /**
     * ToArrayMethodAttribute constructor.
     *
     * @param  ?string  $key  The name of the property to call the method on.
     * @param  ?string  $method  The name of the method to call on the property's object during array conversion.
     * @param  array<int, mixed>  $args  An optional array of arguments to pass to the specified method.
     */
    public function __construct(
        public readonly ?string $key = null,
        public readonly ?string $method = null,
        public readonly array $args = []
    ) {}
}
