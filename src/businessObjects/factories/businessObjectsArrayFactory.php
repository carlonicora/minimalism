<?php
namespace carlonicora\minimalism\businessObjects\factories;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\businessObjects\interfaces\businessObjectsArrayInterface;
use carlonicora\minimalism\exceptions\configurationException;

class businessObjectsArrayFactory {

    /** @var abstractConfigurations  */
    protected abstractConfigurations $configurations;

    /**
     * businessObjectsArrayFactory constructor.
     * @param abstractConfigurations $configurations
     */
    public function __construct(abstractConfigurations $configurations) {
        $this->configurations = $configurations;
    }

    /**
     * @param string $businessObjectsArrayClass
     * @return businessObjectsArrayInterface
     * @throws configurationException
     */
    public function create(string $businessObjectsArrayClass): businessObjectsArrayInterface {
        $resourcesFactory = new businessObjectsFactory($this->configurations);
        return new $businessObjectsArrayClass($resourcesFactory);
    }

}