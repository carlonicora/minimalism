<?php
namespace CarloNicora\Minimalism\Objects;

use CarloNicora\Minimalism\Interfaces\ParameterInterface;
use CarloNicora\Minimalism\Interfaces\PositionedParameterInterface;

class PositionedParameter implements ParameterInterface, PositionedParameterInterface
{
    /**
     * PositionedParameter constructor.
     * @param mixed $value
     */
    public function __construct(private mixed $value)
    {
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}