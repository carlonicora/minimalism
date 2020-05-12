<?php
namespace CarloNicora\Minimalism\Core\Services\Interfaces;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;

interface ServiceInterface {
    /**
     *
     */
    public function cleanNonPersistentVariables() : void;

    /**
     * @param array $cookies
     */
    public function unserialiseCookies(array $cookies) : void;

    /**
     * @return array
     */
    public function serialiseCookies(): array;

    /**
     * @param ServicesFactory $services
     */
    public function initialiseStatics(ServicesFactory $services) : void;

    /**
     *
     */
    public function destroyStatics() : void;
}