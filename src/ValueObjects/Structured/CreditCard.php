<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidCreditCardException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

/**
 * A Value Object representing credit card information.
 *
 * This class encapsulates the details of a credit card, including the cardholder's name,
 * card number, expiration date, and CVV code. It includes validation to ensure that the
 * provided data adheres to expected formats and constraints.
 */
final readonly class CreditCard extends AbstractStructuredValueObject
{
    /**
     * CreditCard private constructor.
     *
     * @internal
     *
     * @param  string  $holderName  The name of the cardholder.
     * @param  string  $number  The credit card number.
     * @param  string  $expirationMonth  The expiration month (MM).
     * @param  string  $expirationYear  The expiration year (YYYY).
     * @param  string  $cvv  The card verification value.
     */
    private function __construct(
        public readonly string $holderName,
        public readonly string $number,
        public readonly string $expirationMonth,
        public readonly string $expirationYear,
        public readonly string $cvv
    ) {}

    /**
     * Creates a new CreditCard instance with explicit, validated parameters.
     *
     * This factory validates the expiration month and year to ensure they are within acceptable ranges.
     *
     * @param  string  $holderName  The name of the cardholder.
     * @param  string  $number  The credit card number.
     * @param  string  $expirationMonth  The expiration month (MM).
     * @param  string  $expirationYear  The expiration year (YYYY).
     * @param  string  $cvv  The card verification value.
     * @return self A new, validated CreditCard instance.
     *
     * @throws InvalidCreditCardException If any of the provided data is invalid.
     */
    public static function create(
        string $holderName,
        string $number,
        string $expirationMonth,
        string $expirationYear,
        string $cvv
    ): self {

        if (! preg_match('/^(0[1-9]|1[0-2])$/', $expirationMonth)) {
            throw new InvalidCreditCardException('Expiration month must be between 01 and 12');
        }

        $currentYear = (int) date('Y');
        if ((int) $expirationYear < $currentYear) {
            throw new InvalidCreditCardException('Expiration year cannot be in the past');
        }

        $currentMonth = (int) date('m');
        if ((int) $expirationYear === $currentYear && (int) $expirationMonth < $currentMonth) {
            throw new InvalidCreditCardException('Expiration month cannot be in the past for the current year');
        }

        if (! self::validateNumber($number)) {
            throw new InvalidCreditCardException('Invalid credit card number');
        }

        if (! preg_match('/^\d{3,4}$/', $cvv)) {
            throw new InvalidCreditCardException('CVV must be 3 or 4 digits');
        }

        return new self($holderName, $number, $expirationMonth, $expirationYear, $cvv);
    }

    /**
     * Creates a CreditCard instance from a raw data array.
     *
     * @param  array{holderName?: string, number?: string, expirationMonth?: string, expirationYear?: string, cvv?: string}  $data  The raw data array.
     * @return self A new, validated CreditCard instance.
     *
     * @throws InvalidCreditCardException If required keys are missing.
     */
    public static function fromArray(array $data): self
    {
        $requiredFields = ['holderName', 'number', 'expirationMonth', 'expirationYear', 'cvv'];
        foreach ($requiredFields as $field) {
            if (! isset($data[$field])) {
                throw new InvalidCreditCardException("Missing required field: {$field}");
            }
        }

        /** @phpstan-ignore-next-line */
        $holderName = DataSanitizer::sanitizeString($data['holderName']);
        /** @phpstan-ignore-next-line */
        $number = DataSanitizer::onlyDigits($data['number']);
        /** @phpstan-ignore-next-line */
        $expirationMonth = DataSanitizer::onlyDigits($data['expirationMonth']);
        $expirationMonth = str_pad($expirationMonth, 2, '0', STR_PAD_LEFT);
        /** @phpstan-ignore-next-line */
        $expirationYear = DataSanitizer::onlyDigits($data['expirationYear']);
        /** @phpstan-ignore-next-line */
        $cvv = DataSanitizer::onlyDigits($data['cvv']);

        return self::create(
            holderName: $holderName,
            number: $number,
            expirationMonth: $expirationMonth,
            expirationYear: $expirationYear,
            cvv: $cvv
        );
    }

    /**
     * Validates the credit card number using the Luhn algorithm.
     *
     * @param  string  $number  The credit card number to validate.
     * @return bool True if the number is valid, false otherwise.
     */
    private static function validateNumber(string $number): bool
    {
        $number = preg_replace('/\D/', '', $number);
        $length = strlen($number);

        if ($length < 13 || $length > 19) {
            return false;
        }

        $sum = 0;
        $number = strrev($number);

        for ($i = 0; $i < $length; $i++) {
            $digit = (int) $number[$i];
            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
        }

        return $sum % 10 === 0;
    }
}
