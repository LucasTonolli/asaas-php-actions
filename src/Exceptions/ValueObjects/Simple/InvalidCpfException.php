<?php

namespace AsaasPhpSdk\Exceptions\ValueObjects\Simple;

use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;

/**
 * Represents an error for an invalid CPF value.
 *
 * This exception is typically thrown by the `Cpf` Value Object when a given
 * string does not conform to a valid CPF format (e.g., incorrect number of
 * digits or an invalid checksum).
 */
class InvalidCpfException extends InvalidValueObjectException {}
