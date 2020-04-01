<?php
namespace carlonicora\minimalism\core\services\interfaces;

use carlonicora\minimalism\core\services\factories\servicesFactory;

interface serviceInterface {
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
     * @param servicesFactory $services
     */
    public function initialiseStatics(servicesFactory $services): void;
}