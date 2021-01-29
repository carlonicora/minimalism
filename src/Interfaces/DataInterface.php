<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DataInterface extends ServiceInterface
{
    /**
     * @param string $dbReader
     * @return TableInterface
     */
    public function create(string $dbReader): TableInterface;

    /**
     * @param CacheInterface $cache
     */
    public function setCacheInterface(CacheInterface $cache): void;

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
     * @return int
     */
    public function count(
        string $tableInterfaceClassName,
        string $functionName,
        array $parameters,
    ): int;

    /**
     * @param string $tableInterfaceClassName
     * @param array $records
     * @param CacheBuilderInterface|null $cacheBuilder
     * @param bool $avoidSingleInsert
     */
    public function update(
        string $tableInterfaceClassName,
        array $records,
        ?CacheBuilderInterface $cacheBuilder=null,
        bool $avoidSingleInsert=false
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
     * @param bool $avoidSingleInsert
     * @return array
     */
    public function insert(
        string $tableInterfaceClassName,
        array $records,
        ?CacheBuilderInterface $cacheBuilder=null,
        bool $avoidSingleInsert=false
    ): array;

    /**
     * @param string $tableInterfaceClassName
     * @param string $functionName
     * @param array $parameters
     * @return array|null
     */
    public function run(
        string $tableInterfaceClassName,
        string $functionName,
        array $parameters,
    ): ?array;
}