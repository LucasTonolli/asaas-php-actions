<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidCreditCardException;
use AsaasPhpSdk\Support\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

/**
 * A Value Object representing credit card information.
 *
 * This class encapsulates the details of a credit card, including the cardholder's name,
 * card number, expiration date, and ccv code. It includes validation to ensure that the
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
     * @param  string  $expiryMonth  The expiration month (MM).
     * @param  string  $expiryYear  The expiration year (YYYY).
     * @param  string  $ccv  The card verification value.
     */
    private function __construct(
        public string $holderName,
        public string $number,
        public string $expiryMonth,
        public string $expiryYear,
        public string $ccv
    ) {}

    /**
     * Creates a new CreditCard instance with explicit, validated parameters.
     *
     * This factory validates the expiration month and year to ensure they are within acceptable ranges.
     *
     * @param  string  $holderName  The name of the cardholder.
     * @param  string  $number  The credit card number.
     * @param  string  $expiryMonth  The expiration month (MM).
     * @param  string  $expiryYear  The expiration year (YYYY).
     * @param  string  $ccv  The card verification value.
     * @return self A new, validated CreditCard instance.
     *
     * @throws InvalidCreditCardException If any of the provided data is invalid.
     */
    private static function create(
        string $holderName,
        string $number,
        string $expiryMonth,
        string $expiryYear,
        string $ccv
    ): self {
        if (empty(DataSanitizer::sanitizeString($holderName))) {
            throw new InvalidCreditCardException('Holder name cannot be empty');
        }
        if (! preg_match('/^(0[1-9]|1[0-2])$/', $expiryMonth)) {
            throw new InvalidCreditCardException('Expiration month must be between 01 and 12');
        }

        if (! preg_match('/^\d{4}$/', $expiryYear)) {
            throw new InvalidCreditCardException('Expiration year must be 4 digits (YYYY)');
        }

        $currentYear = (int) date('Y');
        if ((int) $expiryYear < $currentYear) {
            throw new InvalidCreditCardException('Expiration year cannot be in the past');
        }

        $currentMonth = (int) date('m');
        if ((int) $expiryYear === $currentYear && (int) $expiryMonth < $currentMonth) {
            throw new InvalidCreditCardException('Expiration month cannot be in the past for the current year');
        }

        if (! self::validateNumber($number)) {
            throw new InvalidCreditCardException('Invalid credit card number');
        }

        if (! preg_match('/^\d{3,4}$/', $ccv)) {
            throw new InvalidCreditCardException('ccv must be 3 or 4 digits');
        }

        return new self($holderName, $number, $expiryMonth, $expiryYear, $ccv);
    }

    /**
     * Creates a CreditCard instance from a raw data array.
     *
     * @param  array{holderName: string|null, number: string|null, expiryMonth: string|null, expiryYear: string|null, ccv: string|null}  $data  The raw data array.
     * @return self A new, validated CreditCard instance.
     *
     * @throws InvalidCreditCardException If required keys are missing.
     */
    public static function fromArray(array $data): self
    {
        $requiredFields = ['holderName', 'number', 'expiryMonth', 'expiryYear', 'ccv'];
        foreach ($requiredFields as $field) {
            if (! isset($data[$field])) {
                throw new InvalidCreditCardException("Missing required field: {$field}");
            }
        }

        $holderName = DataSanitizer::sanitizeString($data['holderName']) ?? '';
        $number = DataSanitizer::onlyDigits($data['number']) ?? '';
        $expiryMonth = DataSanitizer::onlyDigits($data['expiryMonth']) ?? '';
        $expiryMonth = str_pad($expiryMonth, 2, '0', STR_PAD_LEFT);
        $expiryYear = DataSanitizer::onlyDigits($data['expiryYear']) ?? '';
        $ccv = DataSanitizer::onlyDigits($data['ccv']) ?? '';

        return self::create(
            holderName: $holderName,
            number: $number,
            expiryMonth: $expiryMonth,
            expiryYear: $expiryYear,
            ccv: $ccv
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
