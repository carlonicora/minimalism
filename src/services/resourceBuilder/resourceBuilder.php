<?php
namespace carlonicora\minimalism\services\resourceBuilder;

use carlonicora\minimalism\core\services\abstracts\abstractService;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\resourceBuilder\interfaces\resourceBuilderInterface;

class resourceBuilder extends abstractService {
    /** @var servicesFactory  */
    private servicesFactory $services;

    /**
     * resourceBuilder constructor.
     * @param servicesFactory $services
     */
    public function __construct(servicesFactory $services){
        $this->services = $services;
    }

    /**
     * @param string $objectName
     * @param array $data
     * @return resourceBuilderInterface
     */
    public function create(string $objectName, array $data) : resourceBuilderInterface {
        return new $objectName($this->services, $data);
    }

    /**
     * @param servicesFactory $services
     */
    public function initialiseStatics(servicesFactory $services): void {
        $this->services = $services;
    }
}