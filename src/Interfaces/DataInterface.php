<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DataInterface
{
    /**
     * @param string $dbReader
     * @return TableInterface
     */
    public function create(string $dbReader): TableInterface;

    /**
     * @param string $tableInterfaceClassName
     * @param string $functionName
     * @param array $parameters
     * @param CacheBuilderInterface|null $cacheBuilder
     * @return array
     */
    public function read(
        string $tableInterfaceClassName,
        string $functionName,
        array $parameters,
        ?CacheBuilderInterface $cacheBuilder=null,
    ): array;

    /**
     * @param string $tableInterfaceClassName
     * @param string $functionName
     * @param array $parameters
     * @param CacheBuilderInterface|null $cacheBuilder
     * @return int
     */
    public function count(
        string $tableInterfaceClassName,
        string $functionName,
        array $parameters,
        ?CacheBuilderInterface $cacheBuilder=null,
    ): int;

    /**
     * @param string $tableInterfaceClassName
     * @param array $records
     * @param CacheBuilderInterface|null $cacheBuilder
     */
    public function update(
        string $tableInterfaceClassName,
        array $records,
        ?CacheBuilderInterface $cacheBuilder=null,
    ): void;

    /**
     * @param string $tableInterfaceClassName
     * @param array $records
     * @param CacheBuilderInterface|null $cacheBuilder
     */
    public function delete(
        string $tableInterfaceClassName,
        array $records,
        ?CacheBuilderInterface $cacheBuilder=null,
    ): void;

    /**
     * @param string $tableInterfaceClassName
     * @param array $records
     * @param CacheBuilderInterface|null $cacheBuilder
     * @return array
     */
    public function insert(
        string $tableInterfaceClassName,
        array $records,
        ?CacheBuilderInterface $cacheBuilder=null,
    ): array;
}