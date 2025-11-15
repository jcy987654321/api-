<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;

class FeedbackEncryptionService
{
    private Encrypter $encrypter;

    public function __construct()
    {
        $this->encrypter = Crypt::getFacadeRoot();
    }

    public function encrypt(string $data): string
    {
        return $this->encrypter->encrypt($data);
    }

    public function decrypt(string $encryptedData): string
    {
        return $this->encrypter->decrypt($encryptedData);
    }

    public function encryptEmail(string $email): string
    {
        return $this->encrypt(strtolower($email));
    }

    public function decryptEmail(string $encryptedEmail): string
    {
        return strtolower($this->decrypt($encryptedEmail));
    }

    public function encryptSubject(string $subject): string
    {
        return $this->encrypt(trim($subject));
    }

    public function decryptSubject(string $encryptedSubject): string
    {
        return trim($this->decrypt($encryptedSubject));
    }

    public function encryptContent(string $content): string
    {
        return $this->encrypt(trim($content));
    }

    public function decryptContent(string $encryptedContent): string
    {
        return trim($this->decrypt($encryptedContent));
    }

    public function encryptAdminNotes(string $notes): string
    {
        return $this->encrypt(trim($notes));
    }

    public function decryptAdminNotes(string $encryptedNotes): string
    {
        return trim($this->decrypt($encryptedNotes));
    }
}