<?php
namespace CarloNicora\Minimalism\Interfaces;

interface ParameterInterface
{
    /**
     * ParameterInterface constructor.
     * @param mixed $value
     */
    public function __construct(mixed $value);

    /**
     * @return mixed
     */
    public function getValue(): mixed;
}