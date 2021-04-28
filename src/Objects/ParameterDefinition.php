<?php
namespace CarloNicora\Minimalism\Objects;

class ParameterDefinition
{
    public const PARAMETER_TYPE_NULL=0;
    public const PARAMETER_TYPE_SERVICE=1;
    public const PARAMETER_TYPE_DOCUMENT=2;
    public const PARAMETER_TYPE_SIMPLE=3;
    public const PARAMETER_TYPE_LOADER=4;
    public const PARAMETER_TYPE_PARAMETER=5;
    public const PARAMETER_TYPE_ENCRYPTER_PARAMETER=6;

    /** @var int  */
    private int $type = self::PARAMETER_TYPE_NULL;

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
        private string $name,
        private bool $allowsNull,
        private mixed $defaultValue=null
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
     * @param int $type
     */
    public function setType(int $type): void
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
     * @return int
     */
    public function getType(): int
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