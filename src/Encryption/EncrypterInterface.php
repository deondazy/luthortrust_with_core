<?php

declare(strict_types=1);

namespace Denosys\Core\Encryption;

interface EncrypterInterface
{
    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param bool $serialize
     * @return string
     *
     * @throws EncryptException
     */
    public function encrypt(mixed $value, bool $serialize = true): string;

    /**
     * Decrypt the given payload.
     *
     * @param  string  $payload
     * @param  bool  $unserialize
     * @return mixed
     *
     * @throws DecryptException
     */
    public function decrypt(string $payload, bool $unserialize = true): mixed;

    /**
     * Get the encryption key that the encrypter is currently using.
     *
     * @return string
     */
    public function getKey(): string;
}
