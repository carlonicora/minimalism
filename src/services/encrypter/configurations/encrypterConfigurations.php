<?php
namespace carlonicora\minimalism\services\encrypter\configurations;

use carlonicora\minimalism\exceptions\configurationException;

class encrypterConfigurations {
    /** @var string */
    public string $key;

    /** @var int */
    public int $length;

    /**
     * mailingConfigurations constructor.
     * @throws configurationException
     */
    public function __construct() {
        if (getenv('MINIMALISM_ENCRYPTER_KEY') === null){
            throw new configurationException('encrypter', 'MINIMALISM_ENCRYPTER_KEY is a required configuration');
        }

        $this->key = getenv('MINIMALISM_ENCRYPTER_KEY');

        $length = getenv('MINIMALISM_ENCRYPTER_LENGTH');
        if ($length === null){
            $length = 18;
        }
        $this->length = $length;
    }
}