<?php
namespace carlonicora\minimalism\helpers;

use Exception;

class stringEncrypter
{
    /**
     * @param int $bytes
     * @return string
     * @throws Exception
     */
    public function createEncryptedString(int $bytes): string
    {
        return bin2hex(random_bytes($bytes));
    }
}