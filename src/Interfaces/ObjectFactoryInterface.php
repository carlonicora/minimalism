<?php
namespace CarloNicora\Minimalism\Interfaces;

interface ObjectFactoryInterface
{
    /**
     * @param string $name
     * @param array|null $parameters
     * @return ObjectInterface
     */
    public function create(
        string $name,
        ?array $parameters=null,
    ): ObjectInterface;
}