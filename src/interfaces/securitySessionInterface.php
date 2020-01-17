<?php
namespace carlonicora\minimalism\interfaces;

use Exception;

interface securitySessionInterface {
    /**
     * @param string $publicKey
     * @param string $clientId
     * @return string
     * @throws Exception
     */
    public function getPrivateKey(string $publicKey, string $clientId): string;
}