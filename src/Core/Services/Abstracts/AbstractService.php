<?php
namespace CarloNicora\Minimalism\Core\Services\Abstracts;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceConfigurationsInterface;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceInterface;

class AbstractService implements ServiceInterface {
    /** @var ServicesFactory  */
    protected ServicesFactory $services;

    /**
     * abstractService constructor.
     * @param ServiceConfigurationsInterface $configData
     * @param ServicesFactory $services
     * @noinspection PhpUnusedParameterInspection
     */
    public function __construct(ServiceConfigurationsInterface $configData, ServicesFactory $services) {
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
     * @param ServicesFactory $services
     */
    public function initialiseStatics(ServicesFactory $services): void{
        $this->services = $services;
    }

    /**
     *
     */
    public function destroyStatics() : void {}
}