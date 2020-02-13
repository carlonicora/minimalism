<?php
namespace carlonicora\minimalism\businessObjects\factories;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\businessObjects\interfaces\businessObjectsInterface;
use carlonicora\minimalism\exceptions\configurationException;
use carlonicora\minimalism\helpers\idEncrypter;

class businessObjectsFactory {

    /** @var abstractConfigurations  */
    protected $configurations;

    /** @var idEncrypter  */
    protected $encrypter;

    /**
     * businessObjectsFactory constructor.
     * @param abstractConfigurations $configurations
     * @throws configurationException
     */
    public function __construct(abstractConfigurations $configurations)
    {
        $this->configurations = $configurations;

        if (empty($this->configurations->encrypterKey) || empty($this->configurations->encrypterLength)) {
            throw new configurationException('Encrypter requires a Key and a Length in configuration');
        }

        $this->encrypter = new idEncrypter($this->configurations);
    }

    /**
     * @param string $businessObjectClassName
     * @return businessObjectsInterface
     */
    public function create(string $businessObjectClassName): businessObjectsInterface {
        return new $businessObjectClassName($this->encrypter);
    }

}