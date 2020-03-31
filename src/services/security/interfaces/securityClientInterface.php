<?php
namespace carlonicora\minimalism\services\security\interfaces;

use Exception;

interface securityClientInterface {
    /**
     * @param string $clientId
     * @return string
     * @throws Exception
     */
    public function getSecret(string $clientId): string;
}