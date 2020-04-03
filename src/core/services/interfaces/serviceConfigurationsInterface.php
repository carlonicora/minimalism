<?php
namespace carlonicora\minimalism\core\services\interfaces;

interface serviceConfigurationsInterface {
    /**
     * @return array
     */
    public function getDependencies() : array;
}