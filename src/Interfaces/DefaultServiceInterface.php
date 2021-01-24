<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DefaultServiceInterface
{
    /**
     * @return array
     */
    public function getDelayedServices(): array;
}