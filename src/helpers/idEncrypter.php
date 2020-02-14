<?php
namespace carlonicora\minimalism\helpers;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use Hashids\Hashids;

class idEncrypter {
    /** @var Hashids */
    private $hashids;

    /**
     * idEncrypter constructor.
     * @param abstractConfigurations $configurations
     */
    public function __construct(abstractConfigurations $configurations) {
        $this->hashids = new Hashids($configurations->encrypterKey, $configurations->encrypterLength);
    }

    /**
     * @param int $id
     * @return string
     */
    public function encryptId(int $id): string {
        return $this->hashids->encodeHex($id);
    }

    /**
     * @param string $encryptedId
     * @return int
     */
    public function decryptId(string $encryptedId): int {
        return (int)$this->hashids->decodeHex($encryptedId);
    }
}