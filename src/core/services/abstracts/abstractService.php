<?php
namespace carlonicora\minimalism\core\services\abstracts;

use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceInterface;

class abstractService implements serviceInterface {
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
    public function initialiseStatics(servicesFactory $services): void{}
}