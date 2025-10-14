<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments;

use AsaasPhpSdk\DTOs\Base\AbstractDTO;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Structured\Callback;
use AsaasPhpSdk\ValueObjects\Structured\Discount;
use AsaasPhpSdk\ValueObjects\Structured\Interest;
use AsaasPhpSdk\ValueObjects\Structured\Split;
use AsaasPhpSdk\DTOs\Attributes\ToArrayMethodAttribute;
use AsaasPhpSdk\Exceptions\DTOs\Payments\InvalidPaymentDataException;
use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;
use AsaasPhpSdk\ValueObjects\Structured\Fine;

final class CreatePaymentDTO extends AbstractDTO
{
	private function __construct(
		public readonly string $customer,
		public readonly BillingTypeEnum $billingType,
		public readonly float $value,
		#[ToArrayMethodAttribute(method: 'format', args: ['Y-m-d'])]
		public readonly \DateTimeImmutable $dueDate,
		public readonly ?string $description = null,
		public readonly ?int $daysAfterDueDateToRegistrationCancellation = null,
		public readonly ?string $externalReference = null,
		public readonly ?int $installmentCount = null,
		public readonly ?float $totalValue = null, // Only for installments and if filled, it's unnecessary installment value
		public readonly ?float $installmentValue = null,
		#[ToArrayMethodAttribute('toArray')]
		public readonly ?Discount $discount = null,
		#[ToArrayMethodAttribute('toArray')]
		public readonly ?Interest $interest = null,
		#[ToArrayMethodAttribute('toArray')]
		public readonly ?Fine $fine = null,
		public readonly ?bool $postalService = null,
		#[ToArrayMethodAttribute('toArray')]
		public readonly ?Split $split = null,
		#[ToArrayMethodAttribute('toArray')]
		public readonly ?Callback $callback = null
	) {}

	public static function fromArray(array $data): self
	{
		$sanitizedData = self::sanitize($data);
		$validatedData = self::validate($sanitizedData);

		return new self(...$validatedData);
	}

	protected static function sanitize(array $data): array
	{
		return [
			'customer' => DataSanitizer::sanitizeString($data['customer'] ?? null),
			'billingType' => $data['billingType'] ?? null,
			'value' => DataSanitizer::sanitizeFloat($data['value'] ?? null),
			'dueDate' => $data['dueDate'] ?? null,
			'description' => self::optionalString($data, 'description'),
			'daysAfterDueDateToRegistrationCancellation' => self::optionalInteger($data, 'daysAfterDueDateToRegistrationCancellation'),
			'externalReference' => self::optionalString($data, 'externalReference'),
			'installmentCount' => self::optionalInteger($data, 'installmentCount'),
			'totalValue' => self::optionalFloat($data, 'totalValue'),
			'installmentValue' => self::optionalFloat($data, 'installmentValue'),
			'discount' => $data['discount'] ?? null,
			'interest' => $data['interest'] ?? null,
			'fine' => $data['fine'] ?? null,
			'postalService' => self::optionalBoolean($data, 'postalService'),
			'split' => $data['split'] ?? null,
			'callback' => $data['callback'] ?? null,
		];
	}
	private static function validate(array $data): array
	{
		if ($data['customer'] === null) {
			throw InvalidPaymentDataException::missingField('customer');
		}

		if (!isset($data['billingType'])) {
			throw InvalidPaymentDataException::missingField('billingType');
		}

		if ($data['value'] === null || $data['value'] <= 0) {
			throw new InvalidPaymentDataException('Value must be greater than 0');
		}

		if ($data['dueDate'] === null) {
			throw InvalidPaymentDataException::missingField('dueDate');
		}

		if ($data['billingType'] instanceof BillingTypeEnum) {
			$billingType = $data['billingType'];
		} else {
			$billingType = BillingTypeEnum::tryFromString((string) $data['billingType']);
		}

		if ($billingType === null) {
			throw new InvalidPaymentDataException('Invalid billing type');
		}
		$data['billingType'] = $billingType;

		try {
			$data['dueDate'] = $data['dueDate'] instanceof \DateTimeImmutable
				? $data['dueDate']
				: new \DateTimeImmutable((string) $data['dueDate']);
		} catch (\Exception $e) {
			throw new InvalidPaymentDataException('Invalid due date format', 0, $e);
		}

		try {
			self::validateStructuredValueObject($data, 'discount', Discount::class);
			self::validateStructuredValueObject($data, 'interest', Interest::class);
			self::validateStructuredValueObject($data, 'fine', Fine::class);
			self::validateStructuredValueObject($data, 'split', Split::class);
			self::validateStructuredValueObject($data, 'callback', Callback::class);
		} catch (InvalidValueObjectException $e) {
			throw new InvalidPaymentDataException($e->getMessage(), 0, $e);
		}

		if ($data['split'] instanceof Split) {
			try {
				$data['split']->validateFor($data['value']);
			} catch (\Throwable $e) {
				throw new InvalidPaymentDataException($e->getMessage(), 0, $e);
			}
		}


		return $data;
	}
}
