<?php
namespace carlonicora\minimalism\services\resourceBuilder\factories;

use carlonicora\minimalism\core\exceptions\configurationException;
use carlonicora\minimalism\core\services\abstracts\abstractServiceFactory;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\resourceBuilder\configurations\resourceBuilderConfigurations;
use carlonicora\minimalism\services\resourceBuilder\resourceBuilder;

class serviceFactory extends abstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param servicesFactory $services
     * @throws configurationException
     */
    public function __construct(servicesFactory $services) {
        $this->configData = new resourceBuilderConfigurations();

        parent::__construct($services);
    }

    /**
     * @param servicesFactory $services
     * @return resourceBuilder
     */
    public function create(servicesFactory $services) : resourceBuilder {
        return new resourceBuilder($this->configData, $services);
    }
}