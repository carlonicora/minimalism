<?php
namespace carlonicora\minimalism\core\services\abstracts;

use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceConfigurationsInterface;
use carlonicora\minimalism\core\services\interfaces\serviceInterface;

class abstractService implements serviceInterface {
    /** @var servicesFactory  */
    protected servicesFactory $services;

    /**
     * abstractService constructor.
     * @param serviceConfigurationsInterface $configData
     * @param servicesFactory $services
     * @noinspection PhpUnusedParameterInspection
     */
    public function __construct(serviceConfigurationsInterface $configData, servicesFactory $services) {
        $this->services = $services;
    }

    /**
     *
     */
    public function cleanNonPersistentVariables(): void {}

    /**
     * @param array $cookies
     */
    public function unserialiseCookies(array $cookies): void{}

    /**
     * @return array
     */
    public function serialiseCookies(): array{
        return [];
    }

    /**
     * @param servicesFactory $services
     */
    final public function initialiseStatics(servicesFactory $services): void{
        $this->services = $services;
    }

    /**
     *
     */
    public function destroyStatics() : void {}
}