<?php
namespace carlonicora\minimalism\services\resourceBuilder;

use carlonicora\minimalism\core\services\abstracts\abstractService;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceConfigurationsInterface;
use carlonicora\minimalism\services\resourceBuilder\configurations\resourceBuilderConfigurations;
use carlonicora\minimalism\services\resourceBuilder\interfaces\resourceBuilderInterface;

class resourceBuilder extends abstractService {
    /** @var resourceBuilderConfigurations  */
    private resourceBuilderConfigurations $configData;

    /**
     * abstractApiCaller constructor.
     * @param serviceConfigurationsInterface $configData
     * @param servicesFactory $services
     */
    public function __construct(serviceConfigurationsInterface $configData, servicesFactory $services) {
        parent::__construct($configData, $services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->configData = $configData;
    }

    /**
     * @param string $objectName
     * @param array $data
     * @return resourceBuilderInterface
     */
    public function create(string $objectName, array $data) : resourceBuilderInterface {
        return new $objectName($this->services, $data);
    }
}