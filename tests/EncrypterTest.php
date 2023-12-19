<?php

use PHPUnit\Framework\TestCase;
use Denosys\Core\Encryption\Encrypter;
use Denosys\Core\Encryption\DecryptException;
use Denosys\Core\Encryption\EncryptException;
use Denosys\Core\Encryption\InvalidKeyException;

class EncrypterTest extends TestCase
{
    private string $validKey;
    private Encrypter $encrypter;

    protected function setUp(): void
    {
        // Generate a valid key
        $this->validKey = Encrypter::generateKey();
        $this->encrypter = new Encrypter($this->validKey);
    }

    /**
     * @covers Encrypter::__construct
     */
    public function test_constructor_with_valid_key(): void
    {
        $this->assertInstanceOf(Encrypter::class, $this->encrypter);
    }

    /**
     * @covers Encrypter::__construct
     */
    public function test_constructor_with_short_key_length(): void
    {
        $this->expectException(InvalidKeyException::class);
        new Encrypter(base64_encode('short_key'));
    }

    /**
     * @covers Encrypter::__construct
     */
    public function test_constructor_with_invalid_key(): void
    {
        $this->expectException(InvalidKeyException::class);
        new Encrypter('invalid_key');
    }

    /**
     * @covers Encrypter::isValidKey
     */
    public function test_is_valid_key_with_valid_length(): void
    {
        $this->assertTrue(Encrypter::isValidKey(
            sodium_base642bin($this->validKey, SODIUM_BASE64_VARIANT_ORIGINAL))
        );
    }

    /**
     * @covers Encrypter::isValidKey
     */
    public function test_is_valid_key_with_invalid_length(): void
    {
        $this->assertFalse(Encrypter::isValidKey('short_key'));
    }

    /**
     * @covers Encrypter::encrypt
     * @dataProvider encryption_data_provider
     */
    public function test_encrypt_with_valid_data_and_serialization(mixed $data, bool $serialize): void
    {
        $encrypted = $this->encrypter->encrypt($data, $serialize);
        $this->assertNotEquals($data, $encrypted);
        $this->assertIsString($encrypted);
    }

    public function encryption_data_provider(): array
    {
        return [
            ['test_data', true],
            [123, true],
            [['test_data', 123, true], true],
            // Add more test cases as needed
        ];
    }

    /**
     * @covers Encrypter::encrypt
     */
    public function test_encrypt_with_valid_data_without_serialization(): void
    {
        $data = 'test_data';
        $encrypted = $this->encrypter->encrypt($data, false);
        $this->assertNotEquals($data, $encrypted);
        $this->assertIsString($encrypted);
    }

    /**
     * @covers Encrypter::encrypt
     */
    public function test_encrypt_with_invalid_data(): void
    {
        $this->expectException(EncryptException::class);
        $this->encrypter->encrypt([], false);
    }

    /**
     * @covers Encrypter::decrypt
     */
    public function test_decrypt_with_valid_encrypted_data_and_unserialization(): void
    {
        $data = 'test_data';
        $encrypted = $this->encrypter->encrypt($data, true);
        $decrypted = $this->encrypter->decrypt($encrypted, true);
        $this->assertEquals($data, $decrypted);
    }

    /**
     * @covers Encrypter::decrypt
     */
    public function test_decrypt_with_valid_encrypted_data_without_unserialization(): void
    {
        $data = 'test_data';
        $encrypted = $this->encrypter->encrypt($data, false);
        $decrypted = $this->encrypter->decrypt($encrypted, false);
        $this->assertEquals($data, $decrypted);
    }

    /**
     * @covers Encrypter::decrypt
     */
    public function test_decrypt_with_invalid_encrypted_data(): void
    {
        $this->expectException(DecryptException::class);
        $this->encrypter->decrypt('invalid_data', true);
    }

    /**
     * @covers Encrypter::generateKey
     */
    public function test_generate_key(): void
    {
        $key = Encrypter::generateKey();
        $this->assertTrue(Encrypter::isValidKey(
            sodium_base642bin($key, SODIUM_BASE64_VARIANT_ORIGINAL))
        );
    }

    /**
     * @covers Encrypter::getKey
     */
    public function test_get_key(): void
    {
        $key = Encrypter::generateKey();
        $encrypter = new Encrypter($key);
        $this->assertEquals($key, $encrypter->getKey());
    }

}
