<?php

use Denosys\Core\Encryption\DecryptException;
use Denosys\Core\Encryption\Encrypter;
use Denosys\Core\Encryption\EncryptException;
use Denosys\Core\Encryption\InvalidKeyException;

beforeEach(function () {
    $this->validKey = Encrypter::generateKey();
    $this->encrypter = new Encrypter($this->validKey);
});

test('encryption with valid key', function () {
    expect($this->encrypter)->toBeInstanceOf(Encrypter::class);
});

test('encryption with short key length', function () {
    new Encrypter(base64_encode('short_key'));
})->throws(InvalidKeyException::class);

test('encryption with invalid key', function () {
    new Encrypter('invalid_key');
})->throws(InvalidKeyException::class);

test('isValidKey with valid key length', function () {
    expect(Encrypter::isValidKey(
        sodium_base642bin($this->validKey, SODIUM_BASE64_VARIANT_ORIGINAL)
    ))->toBeTrue();
});

test('isValidKey with invalid key length', function () {
    expect(Encrypter::isValidKey('short_key'))->toBeFalse();
});

test('encrypt with valid data and serialization', function ($data, $serialize) {
    $encrypted = $this->encrypter->encrypt($data, $serialize);
    expect($encrypted)->not->toEqual($data)->and($encrypted)->toBeString();
})->with([
    ['test_data', true],
    [123, true],
    [['test_data', 123, true], true]
]);

test('encrypt with valid data without serialization', function () {
    $data = 'test_data';
    $encrypted = $this->encrypter->encrypt($data, false);
    expect($encrypted)->not->toEqual($data)->and($encrypted)->toBeString();
});

test('encrypt with invalid data', function () {
    $this->encrypter->encrypt([], false);
})->throws(EncryptException::class);

test('decrypt with valid encrypted data and unserialization', function () {
    $data = 'test_data';
    $encrypted = $this->encrypter->encrypt($data, true);
    $decrypted = $this->encrypter->decrypt($encrypted, true);
    expect($data)->toEqual($decrypted);
});

test('decrypt with valid encrypted data without unserialization', function () {
    $data = 'test_data';
    $encrypted = $this->encrypter->encrypt($data, false);
    $decrypted = $this->encrypter->decrypt($encrypted, false);
    expect($data)->toEqual($decrypted);
});

test('decrypt with invalid encrypted data', function () {
    $this->encrypter->decrypt('invalid_data', true);
})->throws(DecryptException::class);

test('generate key', function () {
    $key = Encrypter::generateKey();
    $isValidKey = Encrypter::isValidKey(
        sodium_base642bin($key, SODIUM_BASE64_VARIANT_ORIGINAL)
    );
    expect($isValidKey)->toBeTrue();
});

test('get key', function () {
    $key = Encrypter::generateKey();
    $encrypter = new Encrypter($key);
    $getKey = $encrypter->getKey();
    expect($getKey)->toEqual($key);
});
