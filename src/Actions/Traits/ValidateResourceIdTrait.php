<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Traits;

/**
 * Trait for validating and normalizing resource IDs.
 */
trait ValidateResourceIdTrait
{
	/**
	 * Validates and normalizes a resource ID.
	 *
	 * @param  string  $id  The ID to validate.
	 * @param  string  $resourceName  The name of the resource (for error messages).
	 * @return string The normalized ID.
	 *
	 * @throws \InvalidArgumentException If the ID is empty.
	 */
	private function validateAndNormalizeId(string $id, string $resourceName): string
	{
		$normalized = trim($id);

		if ($normalized === '') {
			throw new \InvalidArgumentException("{$resourceName} ID cannot be empty");
		}

		return $normalized;
	}
}
