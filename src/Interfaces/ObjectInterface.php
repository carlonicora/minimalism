<?php
namespace CarloNicora\Minimalism\Interfaces;

interface ObjectInterface
{
    /**
     * @param array|null $parameters
     */
    public function __construct(
        ?array $parameters=null,
    );

    /**
     * @return ObjectFactoryInterface|string
     */
    public function getObjectFactoryClass(
    ): ObjectFactoryInterface|string;
}