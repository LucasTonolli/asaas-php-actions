<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Payments;

use AsaasPhpSdk\DTOs\Attributes\SerializeAs;
use AsaasPhpSdk\DTOs\Base\AbstractDTO;
use AsaasPhpSdk\DTOs\Payments\Enums\BillingTypeEnum;
use AsaasPhpSdk\DTOs\Payments\Enums\InvoiceStatusEnum;
use AsaasPhpSdk\DTOs\Payments\Enums\PaymentStatusEnum;
use AsaasPhpSdk\Exceptions\InvalidDateRangeException;

/**
 * A "Lenient" Data Transfer Object for filtering and paginating payments.
 * 
 * This DTO is designed for flexibility. It sanitizes input data but does not
 * throw exceptions for invalid filter values. Instead, invalid or malformed
 * filters are silently ignored (converted to null), allowing for a robust
 * search experience without generating errors.
 */
final class ListPaymentsDTO extends AbstractDTO
{
	/**
	 * Private constructor to enforce object creation via the static `fromArray` factory method.
	 * 
	 * @param  ?string  $installment  The installment number.
	 * @param  ?string  $offset  The number of payments to skip.
	 * @param  ?int  $limit  The maximum number of payments to return.
	 * @param  ?string  $customer  The ID of the customer to whom the payment belongs.
	 * @param  ?string  $customerGroupName  The name of the customer group to whom the payment belongs.
	 * @param  ?BillingTypeEnum  $billingType  The payment method.
	 * @param  ?PaymentStatusEnum  $status  The status of the payment.
	 * @param  ?string  $subscription  The ID of the subscription to which the payment belongs.
	 * @param  ?string  $externalReference  A unique external identifier.
	 * @param  ?\DateTimeImmutable  $paymentDate  The payment's due date.
	 * @param  ?InvoiceStatusEnum  $invoiceStatus  The status of the invoice.
	 * @param  ?bool  $anticipated  Indicates if the payment is anticipated.
	 * @param  ?bool  $anticipable  Indicates if the payment is anticipable.
	 * @param  ?\DateTimeImmutable  $dateCreatedStart  The start date of the payment creation date range.
	 * @param  ?\DateTimeImmutable  $dateCreatedEnd  The end date of the payment creation date range.
	 * @param  ?\DateTimeImmutable  $paymentDateStart  The start date of the payment date range.
	 * @param  ?\DateTimeImmutable  $paymentDateEnd  The end date of the payment date range.
	 * @param  ?\DateTimeImmutable  $dueDateStart  The start date of the payment due date range.
	 * @param  ?\DateTimeImmutable  $dueDateEnd  The end date of the payment due date range.
	 */
	private function __construct(
		public readonly ?string $installment = null,
		public readonly ?int $offset = null,
		public readonly ?int $limit = null,
		public readonly ?string $customer = null,
		public readonly ?string $customerGroupName = null,
		public readonly ?BillingTypeEnum $billingType = null,
		public readonly ?PaymentStatusEnum $status = null,
		public readonly ?string $subscription = null,
		public readonly ?string $externalReference = null,
		#[SerializeAs(method: 'format', args: ['Y-m-d'])]
		public readonly ?\DateTimeImmutable $paymentDate = null,
		public readonly ?InvoiceStatusEnum $invoiceStatus = null,
		public readonly ?bool $anticipated = null,
		public readonly ?bool $anticipable = null,
		#[SerializeAs(key: 'dateCreated[ge]', method: 'format', args: ['Y-m-d'])]
		public readonly ?\DateTimeImmutable $dateCreatedStart = null,
		#[SerializeAs(key: 'dateCreated[le]', method: 'format', args: ['Y-m-d'])]
		public readonly ?\DateTimeImmutable $dateCreatedEnd = null,
		#[SerializeAs(key: 'paymentDate[ge]', method: 'format', args: ['Y-m-d'])]
		public readonly ?\DateTimeImmutable $paymentDateStart = null,
		#[SerializeAs(key: 'paymentDate[le]', method: 'format', args: ['Y-m-d'])]
		public readonly ?\DateTimeImmutable $paymentDateEnd = null,
		#[SerializeAs(key: 'dueDate[ge]', method: 'format', args: ['Y-m-d'])]
		public readonly ?\DateTimeImmutable $dueDateStart = null,
		#[SerializeAs(key: 'dueDate[le]', method: 'format', args: ['Y-m-d'])]
		public readonly ?\DateTimeImmutable $dueDateEnd = null
	) {}

	/**
	 * Create a new ListPaymentsDTO instance from a raw array of filters. 
	 * 
	 * This factory method takes a raw array and sanitizes it. It does not
	 * perform strict validation and will not throw exceptions for invalid filters.
	 *
	 * @param  array<string, mixed>  $data  Raw filter data.
	 * @return self A new instance of the DTO with sanitized filters.
	 */
	public static function fromArray(array $data): self
	{
		$sanitizedData = self::sanitize($data);
		$validatedData = self::validate($sanitizedData);

		return new self(...$validatedData);
	}

	/**
	 * Sanitizes the raw filter data.
	 * 
	 * @internal
	 * 
	 * @param  array<string, mixed>  $data  The raw filter data.
	 * @return array<string, mixed> The sanitized filter array.
	 */
	protected static function sanitize(array $data): array
	{
		return [
			'installment' => self::optionalString($data, 'installment'),
			'offset' => self::optionalInteger($data, 'offset'),
			'limit' => self::optionalInteger($data, 'limit'),
			'customer' => self::optionalString($data, 'customer'),
			'customerGroupName' => self::optionalString($data, 'customerGroupName'),
			'billingType' => $data['billingType'] ?? null,
			'status' => $data['status'] ?? null,
			'subscription' => self::optionalString($data, 'subscription'),
			'externalReference' => self::optionalString($data, 'externalReference'),
			'paymentDate' => self::optionalDateTime($data, 'paymentDate'),
			'invoiceStatus' => $data['invoiceStatus'] ?? null,
			'anticipated' => self::optionalBoolean($data, 'anticipated'),
			'anticipable' => self::optionalBoolean($data, 'anticipable'),
			'dateCreatedStart' => self::optionalDateTime($data, 'dateCreatedStart'),
			'dateCreatedEnd' => self::optionalDateTime($data, 'dateCreatedEnd'),
			'paymentDateStart' => self::optionalDateTime($data, 'paymentDateStart'),
			'paymentDateEnd' => self::optionalDateTime($data, 'paymentDateEnd'),
			'dueDateStart' => self::optionalDateTime($data, 'dueDateStart'),
			'dueDateEnd' => self::optionalDateTime($data, 'dueDateEnd'),
		];
	}

	/**
	 * Validates the sanitized filter data.
	 * 	
	 * @internal
	 *
	 * @param  array<string, mixed>  $data  The sanitized filter data.
	 * @return array<string, mixed> The validated filter array.
	 * 
	 * @throws InvalidDateRangeException
	 */
	private static function validate(array $data): array
	{
		if (isset($data['limit'])) {
			$data['limit'] = max(1, min(100, $data['limit']));
		}

		if (isset($data['offset']) && $data['offset'] < 0) {
			$data['offset'] = null;
		}

		if (isset($data['billingType'])) {
			$data['billingType'] = BillingTypeEnum::tryFromString($data['billingType']);
		}

		if (isset($data['status'])) {
			$data['status'] = PaymentStatusEnum::tryFromString($data['status']);
		}

		if (isset($data['invoiceStatus'])) {
			$data['invoiceStatus'] = InvoiceStatusEnum::tryFromString($data['invoiceStatus']);
		}

		if (isset($data['dateCreatedStart']) && isset($data['dateCreatedEnd']) && $data['dateCreatedStart'] > $data['dateCreatedEnd']) {
			throw new InvalidDateRangeException('The "dateCreatedStart" must be before "dateCreatedEnd"');
		}

		if (isset($data['paymentDateStart']) && isset($data['paymentDateEnd']) && $data['paymentDateStart'] > $data['paymentDateEnd']) {
			throw new InvalidDateRangeException('The "paymentDateStart" must be before "paymentDateEnd"');
		}

		if (isset($data['dueDateStart']) && isset($data['dueDateEnd']) && $data['dueDateStart'] > $data['dueDateEnd']) {
			throw new InvalidDateRangeException('The "dueDateStart" must be before "dueDateEnd"');
		}

		return $data;
	}
}
