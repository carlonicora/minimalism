<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Factories;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceFactory;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\ParameterValidator\Configurations\ParameterValidatorConfigurations;
use CarloNicora\Minimalism\Services\ParameterValidator\ParameterValidator;

class ServiceFactory extends AbstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param ServicesFactory $services
     * @throws ConfigurationException
     */
    public function __construct(ServicesFactory $services) {
        $this->configData = new ParameterValidatorConfigurations();

        parent::__construct($services);
    }

    /**
     * @param ServicesFactory $services
     * @return ParameterValidator
     */
    public function create(ServicesFactory $services) : ParameterValidator {
        return new ParameterValidator($this->configData, $services);
    }
}