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
 * A "Strict" Data Transfer Object for updating an existing customer.
 *
 * This DTO is designed for partial updates, meaning all its properties are
 * optional. However, any data that is provided will be strictly validated.
 * An `InvalidCustomerDataException` is thrown if any of the provided fields
 * are malformed.
 */
final readonly class UpdateCustomerDTO extends AbstractDTO
{
    /**
     * UpdateCustomerDTO protected constructor.
     *
     * @param  ?string  $name  The customer's new full name.
     * @param  null|Cpf|Cnpj  $cpfCnpj  The customer's new document (CPF or CNPJ).
     * @param  ?Email  $email  The customer's new primary email address.
     * @param  ?Phone  $phone  The customer's new landline phone.
     * @param  ?Phone  $mobilePhone  The customer's new mobile phone.
     * @param  ?string  $address  The new street address.
     * @param  ?string  $addressNumber  The new address number.
     * @param  ?string  $complement  New additional address information.
     * @param  ?string  $province  The new neighborhood or province.
     * @param  ?PostalCode  $postalCode  The new postal code.
     * @param  ?string  $externalReference  A new unique external identifier.
     * @param  ?bool  $notificationDisabled  New setting to disable notifications.
     * @param  ?string  $additionalEmails  A new comma-separated list of additional emails.
     * @param  ?string  $municipalInscription  The new municipal registration number.
     * @param  ?string  $stateInscription  The new state registration number.
     * @param  ?string  $observations  New observations about the customer.
     * @param  ?string  $groupName  The new name of the customer's group.
     * @param  ?string  $company  The new company name.
     * @param  ?bool  $foreignCustomer  The new setting for foreign customer status.
     */
    /** @phpstan-ignore-next-line */
    protected function __construct(
        public ?string $name,
        public null|Cpf|Cnpj $cpfCnpj,
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
     * Sanitizes the raw input data array for the update operation.
     *
     * @internal
     */
    protected static function sanitize(array $data): array
    {
        return [
            'name' => self::optionalString($data, 'name'),
            'cpfCnpj' => $data['cpfCnpj'] ?? null,
            'email' => self::optionalString($data, 'email'),
            'phone' => $data['phone'] ?? null,
            'mobilePhone' => $data['mobilePhone'] ?? null,
            'address' => self::optionalString($data, 'address'),
            'addressNumber' => self::optionalString($data, 'addressNumber'),
            'complement' => self::optionalString($data, 'complement'),
            'province' => self::optionalString($data, 'province')
                ?? self::optionalString($data, 'neighborhood'),
            'postalCode' => $data['postalCode'] ?? null,
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
     * Validates the sanitized data for the update operation.
     *
     * @internal
     *
     * @param  array<string, mixed>  $data  The sanitized data to validate.
     * @return array<string, mixed> The validated data.
     *
     * @throws InvalidCustomerDataException
     */
    protected static function validate(array $data): array
    {

        try {
            if (! ($data['cpfCnpj'] instanceof Cpf || $data['cpfCnpj'] instanceof Cnpj)) {
                if ($data['cpfCnpj'] !== null) {
                    $sanitized = DataSanitizer::onlyDigits($data['cpfCnpj']);
                    $length = strlen($sanitized ?? '');

                    $data['cpfCnpj'] = match ($length) {
                        11 => Cpf::from($data['cpfCnpj']),
                        14 => Cnpj::from($data['cpfCnpj']),
                        default => throw new InvalidValueObjectException('CPF or CNPJ must contain 11 or 14 digits'),
                    };
                }
            }

            self::validateSimpleValueObject($data, 'email', Email::class);
            self::validateSimpleValueObject($data, 'postalCode', PostalCode::class);
            self::validateSimpleValueObject($data, 'phone', Phone::class);
            self::validateSimpleValueObject($data, 'mobilePhone', Phone::class);
        } catch (InvalidValueObjectException $e) {
            throw new InvalidCustomerDataException($e->getMessage(), 400, $e);
        }

        return $data;
    }
}
