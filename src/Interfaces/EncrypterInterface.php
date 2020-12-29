<?php
namespace CarloNicora\Minimalism\Interfaces;

interface EncrypterInterface
{
    /**
     * @param int $id
     * @return string
     */
    public function encrypt(int $id): string;

    /**
     * @param string $encryptedId
     * @return int
     */
    public function decrypt(string $encryptedId): int;
}