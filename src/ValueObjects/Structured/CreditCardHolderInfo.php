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
use Symfony\Component\VarDumper\Cloner\Data;

final readonly class CreditCardHolderInfo extends AbstractStructuredValueObject
{
	private function __construct(
		public string $name,
		public Email $email,
		public Cpf|Cnpj $cpfCnpj,
		public PostalCode $postalCode,
		public string $addressNumber,
		public Phone $phone,
		public ?Phone $mobilePhone,
	) {}

	public static function create(string $name, string $email, string $cpfCnpj, string $postalCode, string $addressNumber, string $phone, ?string $mobilePhone = null): self
	{
		return new self(
			name: $name,
			email: Email::from($email),
			cpfCnpj: match (strlen(DataSanitizer::onlyDigits($cpfCnpj))) {
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

		$data['name'] = DataSanitizer::sanitizeString($data['name']);
		$data['email'] = DataSanitizer::sanitizeString($data['email']);
		$data['cpfCnpj'] = DataSanitizer::onlyDigits($data['cpfCnpj']);
		$data['postalCode'] = DataSanitizer::onlyDigits($data['postalCode']);
		$data['addressNumber'] = DataSanitizer::sanitizeString($data['addressNumber']);
		$data['phone'] = DataSanitizer::onlyDigits($data['phone']);
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
