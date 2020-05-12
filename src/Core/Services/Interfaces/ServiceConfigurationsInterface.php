<?php
namespace CarloNicora\Minimalism\Core\Services\Interfaces;

interface ServiceConfigurationsInterface {
    /**
     * @return array
     */
    public function getDependencies() : array;
}