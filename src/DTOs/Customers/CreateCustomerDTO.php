<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Customers;

use AsaasPhpSdk\DTOs\Attributes\SerializeAs;
use AsaasPhpSdk\DTOs\Base\AbstractDTO;
use AsaasPhpSdk\Exceptions\DTOs\Customers\InvalidCustomerDataException;
use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;
use AsaasPhpSdk\Support\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Simple\Cnpj;
use AsaasPhpSdk\ValueObjects\Simple\Cpf;
use AsaasPhpSdk\ValueObjects\Simple\Email;
use AsaasPhpSdk\ValueObjects\Simple\Phone;
use AsaasPhpSdk\ValueObjects\Simple\PostalCode;

/**
 * A "Strict" Data Transfer Object for creating a new customer.
 *
 * This DTO validates input data rigorously upon creation through the `fromArray`
 * static method. It ensures that an instance of this class can only exist in a
 * valid state, throwing an `InvalidCustomerDataException` if the data is invalid.
 */
final readonly class CreateCustomerDTO extends AbstractDTO
{
    /**
     * Private constructor to enforce object creation via the static `fromArray` factory method.
     *
     * @param  string  $name  The customer's full name.
     * @param  Cpf|Cnpj  $cpfCnpj  The customer's document (CPF or CNPJ) as a Value Object.
     * @param  ?Email  $email  The customer's primary email address as a Value Object.
     * @param  ?Phone  $phone  The customer's landline phone as a Value Object.
     * @param  ?Phone  $mobilePhone  The customer's mobile phone as a Value Object.
     * @param  ?string  $address  The street address.
     * @param  ?string  $addressNumber  The address number.
     * @param  ?string  $complement  Additional address information.
     * @param  ?string  $province  The neighborhood or province.
     * @param  ?PostalCode  $postalCode  The postal code as a Value Object.
     * @param  ?string  $externalReference  A unique external identifier for the customer.
     * @param  ?bool  $notificationDisabled  Disables notifications for the customer if true.
     * @param  ?string  $additionalEmails  A comma-separated list of additional notification emails.
     * @param  ?string  $municipalInscription  The municipal registration number.
     * @param  ?string  $stateInscription  The state registration number.
     * @param  ?string  $observations  Any observations about the customer.
     * @param  ?string  $groupName  The name of the group the customer belongs to.
     * @param  ?string  $company  The company name, if applicable.
     * @param  ?bool  $foreignCustomer  Indicates if the customer is foreign.
     */
    /** @phpstan-ignore-next-line */
    protected function __construct(
        public string $name,
        public Cpf|Cnpj $cpfCnpj,
        public ?Email $email = null,
        public ?Phone $phone = null,
        public ?Phone $mobilePhone = null,
        public ?string $address = null,
        public ?string $addressNumber = null,
        public ?string $complement = null,
        public ?string $province = null,
        #[SerializeAs(method: 'formatted')]
        public ?PostalCode $postalCode = null,
        public ?string $externalReference = null,
        public ?bool $notificationDisabled = null,
        public ?string $additionalEmails = null,
        public ?string $municipalInscription = null,
        public ?string $stateInscription = null,
        public ?string $observations = null,
        public ?string $groupName = null,
        public ?string $company = null,
        public ?bool $foreignCustomer = null
    ) {}

    /**
     * Sanitizes the raw input data array.
     *
     * @internal
     *
     * @param  array<string, mixed>  $data  The raw input data.
     * @return array<string, mixed> The sanitized data array.
     */
    protected static function sanitize(array $data): array
    {
        return [
            'name' => DataSanitizer::sanitizeString($data['name'] ?? ''),
            'cpfCnpj' => $data['cpfCnpj'] ?? null,
            'email' => self::optionalString($data, 'email'),
            'phone' => self::optionalOnlyDigits($data, 'phone'),
            'mobilePhone' => self::optionalOnlyDigits($data, 'mobilePhone'),
            'address' => self::optionalString($data, 'address'),
            'addressNumber' => self::optionalString($data, 'addressNumber'),
            'complement' => self::optionalString($data, 'complement'),
            'province' => self::optionalString($data, 'province')
                ?? self::optionalString($data, 'neighborhood'),
            'postalCode' => self::optionalOnlyDigits($data, 'postalCode'),
            'externalReference' => self::optionalString($data, 'externalReference'),
            'notificationDisabled' => self::optionalBoolean($data, 'notificationDisabled'),
            'additionalEmails' => self::optionalString($data, 'additionalEmails'),
            'municipalInscription' => self::optionalString($data, 'municipalInscription'),
            'stateInscription' => self::optionalString($data, 'stateInscription'),
            'observations' => self::optionalString($data, 'observations'),
            'groupName' => self::optionalString($data, 'groupName'),
            'company' => self::optionalString($data, 'company'),
            'foreignCustomer' => self::optionalBoolean($data, 'foreignCustomer'),
        ];
    }

    /**
     * Validates the sanitized data and converts values to Value Objects.
     *
     * @internal
     *
     * @param  array<string, mixed>  $data  The sanitized data array.
     * @return array<string, mixed> The validated data array with values converted to VOs.
     *
     * @throws InvalidCustomerDataException
     */
    protected static function validate(array $data): array
    {
        if (empty($data['name'])) {
            throw InvalidCustomerDataException::missingField('name');
        }

        if (empty($data['cpfCnpj'])) {
            throw InvalidCustomerDataException::missingField('cpfCnpj');
        }

        try {
            $sanitized = DataSanitizer::onlyDigits($data['cpfCnpj']);
            $length = strlen($sanitized ?? '');

            $data['cpfCnpj'] = match ($length) {
                11 => Cpf::from($data['cpfCnpj']),
                14 => Cnpj::from($data['cpfCnpj']),
                default => throw new InvalidValueObjectException(
                    'CPF or CNPJ must contain 11 or 14 digits'
                ),
            };
        } catch (InvalidValueObjectException $e) {
            throw InvalidCustomerDataException::invalidFormat('cpfCnpj', $e->getMessage());
        }
        try {
            self::validateSimpleValueObject($data, 'email', Email::class);
        } catch (InvalidValueObjectException $e) {
            throw InvalidCustomerDataException::invalidFormat('email', $e->getMessage());
        }

        try {
            self::validateSimpleValueObject($data, 'postalCode', PostalCode::class);
        } catch (InvalidValueObjectException $e) {
            throw InvalidCustomerDataException::invalidFormat('postalCode', $e->getMessage());
        }

        try {
            self::validateSimpleValueObject($data, 'phone', Phone::class);
        } catch (InvalidValueObjectException $e) {
            throw InvalidCustomerDataException::invalidFormat('phone', $e->getMessage());
        }

        try {
            self::validateSimpleValueObject($data, 'mobilePhone', Phone::class);
        } catch (InvalidValueObjectException $e) {
            throw InvalidCustomerDataException::invalidFormat('mobilePhone', $e->getMessage());
        }

        return $data;
    }
}
