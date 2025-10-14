<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Base;

use ReflectionClass;

abstract class AbstractStructuredValueObject
{
	/**
	 * Converts the structured value object into an array.
	 * 
	 * - Recursively calls toArray() for nested StructuredValueObjects.
	 * - Uses value() for simple ValueObjects.
	 * - Removes null values.
	 *
	 * @return array
	 */
	public function toArray(): array
	{
		$reflection = new ReflectionClass($this);
		$properties = $reflection->getProperties();
		$result = [];

		foreach ($properties as $property) {
			// Ensure the property is initialized before trying to get its value
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


	public function equals(AbstractStructuredValueObject $other): bool
	{
		return $this->toArray() === $other->toArray();
	}
}
