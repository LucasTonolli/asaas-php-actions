<?php

use AsaasPhpSdk\Exceptions\ValueObjects\Structured\InvalidCallbackException;
use AsaasPhpSdk\ValueObjects\Structured\Callback;

describe('Callback Value Object', function (): void {
	it('can be created with a valid callback', function (): void {
		$callback = Callback::fromArray([
			'successUrl' => 'https://www.example.com/callback',
			'autoRedirect' => false
		]);
		expect($callback->successUrl)->toBe('https://www.example.com/callback');
		expect($callback->autoRedirect)->toBeFalse();

		$callback = Callback::create('https://www.example.com/callback', false);
		expect($callback->successUrl)->toBe('https://www.example.com/callback');
		expect($callback->autoRedirect)->toBeFalse();
	});

	it('cannot be created with an invalid callback', function (): void {
		expect(fn() => Callback::fromArray([
			'successUrl' => 'https:/www.example'
		]))->toThrow(InvalidCallbackException::class, 'Invalid success URL');

		expect(fn() => Callback::create('http://www.example'))->toThrow(InvalidCallbackException::class, 'Success URL must use HTTPS protocol');
	});

	it('value is required', function (): void {
		expect(fn() => Callback::fromArray([]))->toThrow(InvalidCallbackException::class, 'successUrl is required');
	});

	it('compares the same callback', function (): void {
		$callback1 = Callback::create('https://www.example.com/callback', false);
		$callback2 = Callback::create('https://www.example.com/callback', false);
		$callback3 = Callback::create('https://www.example.com/callback2', true);
		expect($callback1->equals($callback2))->toBeTrue();
		expect($callback1->equals($callback3))->toBeFalse();
	});
});
