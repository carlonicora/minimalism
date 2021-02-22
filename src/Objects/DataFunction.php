<?php
namespace CarloNicora\Minimalism\Objects;

use CarloNicora\Minimalism\Interfaces\CacheBuilderInterface;
use CarloNicora\Minimalism\Interfaces\DataFunctionInterface;

class DataFunction implements DataFunctionInterface
{
    /**
     * DataLoaderFunction constructor.
     * @param int $type
     * @param string $className
     * @param string $functionName
     * @param array|null $parameters
     * @param CacheBuilderInterface|null $cacheBuilder
     */
    public function __construct(
        private int $type,
        private string $className,
        private string $functionName,
        private ?array $parameters = null,
        private ?CacheBuilderInterface $cacheBuilder = null
    )
    {
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getFunctionName(): string
    {
        return $this->functionName;
    }

    /**
     * @return array|null
     */
    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    /**
     * @return CacheBuilderInterface|null
     */
    public function getCacheBuilder(): ?CacheBuilderInterface
    {
        return $this->cacheBuilder;
    }

    /**
     * @param int $parameterKey
     * @param string $parameterValue
     * @deprecated use replaceParameters to replace all parameters at once
     */
    public function replaceParameter(int $parameterKey, string $parameterValue): void
    {
        $this->parameters[$parameterKey] = $parameterValue;
    }

    /**
     * @param array $parameters
     */
    public function replaceParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}