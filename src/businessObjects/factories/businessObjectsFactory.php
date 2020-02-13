<?php
namespace carlonicora\minimalism\businessObjects\factories;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\businessObjects\interfaces\businessObjectsInterface;
use carlonicora\minimalism\exceptions\configurationException;
use Hashids\Hashids;

class businessObjectsFactory {

    /** @var abstractConfigurations  */
    protected $configurations;
    /** @var Hashids  */
    protected $hashIds;

    /**
     * businessObjectsFactory constructor.
     * @param abstractConfigurations $configurations
     * @throws configurationException
     */
    public function __construct(abstractConfigurations $configurations)
    {
        $this->configurations = $configurations;

        if (empty($this->configurations->apiKey) || empty($this->configurations->minHashLength)) {
            throw new configurationException('apiKey and minHashLength settings required in configuration');
        }

        $this->hashIds = new Hashids($this->configurations->apiKey, $this->configurations->minHashLength);
    }

    /**
     * @param string $businessObjectClassName
     * @return businessObjectsInterface
     */
    public function create(string $businessObjectClassName): businessObjectsInterface {
        return new $businessObjectClassName($this->hashIds);
    }

}