<?php
namespace CarloNicora\Minimalism\Interfaces;

interface CacheBuilderFactoryInterface
{
    /**
     * @param string $cacheName
     * @param int|string $identifier
     * @return CacheBuilderInterface
     */
    public function create(
        string $cacheName,
        int|string $identifier
    ): CacheBuilderInterface;

    /**
     * @param string $key
     * @return CacheBuilderInterface
     */
    public function createFromKey(
        string $key
    ): CacheBuilderInterface;

    /**
     * @param string $listName
     * @param string $cacheName
     * @param int|string $identifier
     * @param bool $saveGranular
     * @return CacheBuilderInterface
     */
    public function createList(
        string $listName,
        string $cacheName,
        int|string $identifier,
        bool $saveGranular = true
    ): CacheBuilderInterface;
}