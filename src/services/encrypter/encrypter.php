<?php
namespace carlonicora\minimalism\services\encrypter;

use carlonicora\minimalism\core\services\abstracts\abstractService;
use carlonicora\minimalism\services\encrypter\configurations\encrypterConfigurations;
use Hashids\Hashids;

class encrypter extends abstractService {
    /** @var Hashids */
    private Hashids $hashids;

    /**
     * idEncrypter constructor.
     * @param encrypterConfigurations $configurations
     */
    public function __construct(encrypterConfigurations $configurations) {
        $this->hashids = new Hashids($configurations->key, $configurations->length);
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