<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidCreditCardHolderInfoException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;
use AsaasPhpSdk\ValueObjects\Simple\Cnpj;
use AsaasPhpSdk\ValueObjects\Simple\Cpf;
use AsaasPhpSdk\ValueObjects\Simple\Email;
use AsaasPhpSdk\ValueObjects\Simple\Phone;
use AsaasPhpSdk\ValueObjects\Simple\PostalCode;

/**
 * Value Object representing the credit card holder's information.
 *
 * This class encapsulates the details of a credit card holder, including
 * their name, email, CPF/CNPJ, postal code, address number, phone, and
 * mobile phone. It includes validation to ensure that the provided data
 * adheres to expected formats and constraints.
 */
final readonly class CreditCardHolderInfo extends AbstractStructuredValueObject
{
    /**
     * CreditCardHolderInfo private constructor.
     *
     * @internal
     *
     * @param  string  $name  The name of the credit card holder.
     * @param  Email  $email  The email of the credit card holder.
     * @param  Cpf|Cnpj  $cpfCnpj  The CPF or CNPJ of the credit card holder.
     * @param  PostalCode  $postalCode  The postal code of the credit card holder.
     * @param  string  $addressNumber  The address number of the credit card holder.
     * @param  Phone  $phone  The phone number of the credit card holder.
     * @param  ?Phone  $mobilePhone  The mobile phone number of the credit card holder.
     */
    private function __construct(
        public string $name,
        public Email $email,
        public Cpf|Cnpj $cpfCnpj,
        public PostalCode $postalCode,
        public string $addressNumber,
        public Phone $phone,
        public ?Phone $mobilePhone,
    ) {}

    /**
     * Creates a new CreditCardHolderInfo instance with explicit, validated parameters.
     *
     * This factory validates the provided data to ensure they are within acceptable formats.
     *
     * @param  string  $name  The name of the credit card holder.
     * @param  string  $email  The email of the credit card holder.
     * @param  string  $cpfCnpj  The CPF or CNPJ of the credit card holder.
     * @param  string  $postalCode  The postal code of the credit card holder.
     * @param  string  $addressNumber  The address number of the credit card holder.
     * @param  string  $phone  The phone number of the credit card holder.
     * @param  ?string  $mobilePhone  The mobile phone number of the credit card holder.
     * @return self A new, validated CreditCardHolderInfo instance.
     *
     * @throws InvalidCreditCardHolderInfoException If any of the provided data is invalid.
     */
    private static function create(string $name, string $email, string $cpfCnpj, string $postalCode, string $addressNumber, string $phone, ?string $mobilePhone = null): self
    {
        // Validate name
        if (empty($name)) {
            throw new InvalidCreditCardHolderInfoException('Name cannot be empty');
        }

        if (empty($addressNumber)) {
            throw new InvalidCreditCardHolderInfoException('Address number cannot be empty');
        }
        $digits = DataSanitizer::onlyDigits($cpfCnpj) ?? '';
        return new self(
            name: $name,
            email: Email::from($email),
            cpfCnpj: match (strlen($digits)) {
                11 => Cpf::from($cpfCnpj),
                14 => Cnpj::from($cpfCnpj),
                default => throw new InvalidCreditCardHolderInfoException('CPF or CNPJ must contain 11 or 14 digits'),
            },
            postalCode: PostalCode::from($postalCode),
            addressNumber: $addressNumber,
            phone: Phone::from($phone),
            mobilePhone: $mobilePhone !== null ? Phone::from($mobilePhone) : null,
        );
    }

    /**
     * Creates a CreditCardHolderInfo instance from a raw data array.
     *
     * @param  array{ name: string|null, email: string|null, cpfCnpj: string|null, postalCode: string|null, addressNumber: string|null, phone: string|null, mobilePhone?: string }  $data  The raw data array.
     * @return self A new, validated CreditCardHolderInfo instance.
     *
     * @throws InvalidCreditCardHolderInfoException If required keys are missing.
     */
    public static function fromArray(array $data): self
    {
        $requiredFields = [
            'name',
            'email',
            'cpfCnpj',
            'postalCode',
            'addressNumber',
            'phone',
        ];

        foreach ($requiredFields as $field) {
            if (! isset($data[$field])) {
                throw new InvalidCreditCardHolderInfoException("Missing required field: {$field}");
            }
        }

        $data['name'] = DataSanitizer::sanitizeString($data['name']) ?? '';
        $data['email'] = DataSanitizer::sanitizeString($data['email']) ?? '';
        $data['cpfCnpj'] = DataSanitizer::onlyDigits($data['cpfCnpj']) ?? '';
        $data['postalCode'] = DataSanitizer::onlyDigits($data['postalCode']) ?? '';
        $data['addressNumber'] = DataSanitizer::sanitizeString($data['addressNumber']) ?? '';
        $data['phone'] = DataSanitizer::onlyDigits($data['phone']) ?? '';
        $data['mobilePhone'] = DataSanitizer::onlyDigits($data['mobilePhone'] ?? null);

        return self::create(
            name: $data['name'],
            email: $data['email'],
            cpfCnpj: $data['cpfCnpj'],
            postalCode: $data['postalCode'],
            addressNumber: $data['addressNumber'],
            phone: $data['phone'],
            mobilePhone: $data['mobilePhone'] ?? null,
        );
    }
}
