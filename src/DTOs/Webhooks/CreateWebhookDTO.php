<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Webhooks;

use AsaasPhpSdk\DTOs\Base\AbstractDTO;
use AsaasPhpSdk\DTOs\Webhooks\Enums\SendTypeEnum;
use AsaasPhpSdk\DTOs\Webhooks\Enums\EventEnum;
use AsaasPhpSdk\Exceptions\DTOs\Webhook\InvalidWebhookDataException;
use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;
use AsaasPhpSdk\Support\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Simple\Email;

/**
 * A "Strict" Data Transfer Object for creating a new webhook.
 * 
 * This DTO validates the structure and format of the webhook data upon creation.
 * It ensures that an instance of this class can only exist in a valid state,
 * throwing an `InvalidWebhookDataException` if any rule is violated.
 * 
 */
final readonly class CreateWebhookDTO extends AbstractDTO
{

	/**
	 * Private constructor to enforce object creation via the static `fromArray` factory method.
	 * 
	 * @param  ?string  $name  The name of the webhook.
	 * @param  ?string  $url  The URL to which the webhook will be sent.
	 * @param  ?Email  $email  The email to which the webhook will be sent.
	 * @param  ?bool  $enabled  Whether the webhook is enabled or not.
	 * @param  ?bool  $interrupted  Whether the webhook is interrupted or not.
	 * @param  ?int  $apiVersion  The API version to use for the webhook.
	 * @param  ?string  $authToken  The authentication token for the webhook.
	 * @param  ?SendTypeEnum  $sendType  The send type for the webhook.
	 * @param  EventEnum[]|null  $events  The events for the webhook.
	 */
	private function __construct(
		public ?string $name = null,
		public ?string $url = null,
		public ?Email $email = null,
		public ?bool $enabled = null,
		public ?bool $interrupted = null,
		public ?int $apiVersion = null,
		public ?string $authToken = null,
		public ?SendTypeEnum $sendType = null,
		public ?array $events = null
	) {}

	public static function fromArray(array $data): self
	{
		$sanitizedData = self::sanitize($data);
		$validatedData = self::validate($sanitizedData);

		return new self(
			...$validatedData
		);
	}
	/**
	 * @internal
	 */
	protected static function sanitize(array $data): array
	{
		return [
			'name' => DataSanitizer::sanitizeString($data['name'] ?? null),
			'url' => DataSanitizer::sanitizeString($data['url'] ?? null),
			'email' => $data['email'] ?? null,
			'enabled' => DataSanitizer::sanitizeBoolean($data['enabled']),
			'interrupted' => DataSanitizer::sanitizeBoolean($data['interrupted'] ?? null),
			'apiVersion' => DataSanitizer::sanitizeInteger($data['apiVersion'] ?? null),
			'authToken' => DataSanitizer::sanitizeString($data['authToken'] ?? null),
			'sendType' => $data['sendType'] ?? null,
			'events' => $data['events'] ?? null,
		];
	}

	/**
	 * Validates the structure and format of the webhook data.
	 * 
	 * @internal
	 * 
	 * @param  array<string, mixed>  $data  The data to validate.
	 * 
	 * @return array<string, mixed> The validated data.
	 * 
	 * @throws InvalidWebhookDataException|InvalidValueObjectException
	 */
	private static function validate(array $data): array
	{
		if (!(empty($data['url']))) {
			if (! filter_var($data['url'], FILTER_VALIDATE_URL)) {
				throw new InvalidWebhookDataException('Invalid URL');
			}

			$scheme = parse_url($data['url'], PHP_URL_SCHEME);
			if (strtolower((string) $scheme) !== 'https') {
				throw new InvalidWebhookDataException('Success URL must use HTTPS protocol');
			}
		}

		if (!empty($data['email'])) {
			try {
				$data['email'] = Email::from($data['email']);
			} catch (InvalidValueObjectException $e) {
				throw new InvalidWebhookDataException('Invalid email', 400, $e);
			}
		}

		if (!empty($data['sendType'])) {
			$data['sendType'] = SendTypeEnum::tryFromString($data['sendType']);

			if ($data['sendType'] === null) {
				throw new InvalidWebhookDataException('Invalid send type');
			}
		}

		if (!empty($data['events'])) {
			foreach ($data['events'] as $event) {
				$eventEnum = EventEnum::tryFromString($event);
				if ($eventEnum === null) {
					throw new InvalidWebhookDataException("Invalid event {$event}");
				}
			}
		}

		return $data;
	}
}
