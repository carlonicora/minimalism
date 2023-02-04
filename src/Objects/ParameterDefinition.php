<?php
namespace CarloNicora\Minimalism\Objects;

use CarloNicora\Minimalism\Enums\ParameterType;

class ParameterDefinition
{
    /** @var ParameterType  */
    private ParameterType $type = ParameterType::Null;

    /** @var string|null  */
    private ?string $identifier=null;

    /** @var bool  */
    private bool $isPositionedParameter=false;

    /**
     * MethodDefinition constructor.
     * @param string $name
     * @param bool $allowsNull
     * @param mixed|null $defaultValue
     */
    public function __construct(
        private readonly string $name,
        private readonly bool   $allowsNull,
        private readonly mixed  $defaultValue=null
    )
    {
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(
        string $identifier
    ): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @param bool $isPositionedParameter
     */
    public function setIsPositionedParameter(
        bool $isPositionedParameter
    ): void
    {
        $this->isPositionedParameter = $isPositionedParameter;
    }

    /**
     * @param ParameterType $type
     */
    public function setType(ParameterType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @return ParameterType
     */
    public function getType(): ParameterType
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    /**
     * @return bool
     */
    public function isPositionedParameter(): bool
    {
        return $this->isPositionedParameter;
    }
}