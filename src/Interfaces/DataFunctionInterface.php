<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DataFunctionInterface
{
    public const TYPE_TABLE=1;
    public const TYPE_LOADER=2;
    
    /**
     * DataFunctionInterface constructor.
     * @param int $type
     * @param string $className
     * @param string $functionName
     * @param array|null $parameters
     * @param CacheBuilderInterface|null $cacheBuilder
     */
    public function __construct(
        int $type,
        string $className,
        string $functionName,
        ?array $parameters=null,
        ?CacheBuilderInterface $cacheBuilder=null,
    );

    /**
     * @return int
     */
    public function getType(): int;

    /**
     * @return string
     */
    public function getClassName(): string;

    /**
     * @return string
     */
    public function getFunctionName(): string;

    /**
     * @return array|null
     */
    public function getParameters(): ?array;

    /**
     * @return CacheBuilderInterface|null
     */
    public function getCacheBuilder(): ?CacheBuilderInterface;

    /**
     * @param int $parameterKey
     * @param string $parameterValue
     */
    public function replaceParameter(int $parameterKey, string $parameterValue): void;
}