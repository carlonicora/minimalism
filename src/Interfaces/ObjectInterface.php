<?php
namespace CarloNicora\Minimalism\Interfaces;

interface ObjectInterface
{
    /**
     * @return ObjectFactoryInterface|string
     */
    public function getObjectFactoryClass(
    ): ObjectFactoryInterface|string;
}