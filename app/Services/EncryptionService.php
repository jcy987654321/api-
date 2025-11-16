<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use RuntimeException;

class EncryptionService
{
    public function generateRsaKeyPair(): array
    {
        $privKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        if (!$privKey) {
            throw new RuntimeException('Failed to generate RSA key pair');
        }

        openssl_pkey_export($privKey, $privKeyStr);
        $pubKey = openssl_pkey_get_details($privKey);
        $pubKeyStr = $pubKey['key'];

        return [
            'public_key' => $pubKeyStr,
            'private_key' => $privKeyStr,
        ];
    }

    public function decryptRsa(string $encrypted, string $privateKey): string
    {
        $privKey = openssl_pkey_get_private($privateKey);

        if (!$privKey) {
            throw new RuntimeException('Invalid private key');
        }

        $decrypted = '';
        $success = openssl_private_decrypt(
            base64_decode($encrypted),
            $decrypted,
            $privKey,
            OPENSSL_RAA_PADDING
        );

        if (!$success) {
            throw new RuntimeException('RSA decryption failed');
        }

        return $decrypted;
    }

    public function encryptAes(string $data): string
    {
        return Crypt::encryptString($data);
    }

    public function decryptAes(string $encrypted): string
    {
        try {
            return Crypt::decryptString($encrypted);
        } catch (\Exception $e) {
            throw new RuntimeException('AES decryption failed: ' . $e->getMessage());
        }
    }

    public function hashPassword(string $password): string
    {
        return bcrypt($password);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
