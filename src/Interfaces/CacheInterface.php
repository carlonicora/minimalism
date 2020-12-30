<?php
namespace CarloNicora\Minimalism\Interfaces;

interface CacheInterface
{
    /**
     * @return CacheBuilderFactoryInterface
     */
    public function getCacheBuilderFactory(): CacheBuilderFactoryInterface;

    /**
     * @return bool
     */
    public function useCaching(): bool;

    /**
     * @param CacheBuilderInterface $builder
     * @param string $data
     * @param int $cacheBuilderType
     */
    public function save(CacheBuilderInterface $builder, string $data, int $cacheBuilderType): void;

    /**
     * @param CacheBuilderInterface $builder
     * @param array $data
     * @param int $cacheBuilderType
     */
    public function saveArray(CacheBuilderInterface $builder, array $data, int $cacheBuilderType): void;

    /**
     * @param CacheBuilderInterface $builder
     * @param int $cacheBuilderType
     * @return string|null
     */
    public function read(CacheBuilderInterface $builder, int $cacheBuilderType): ?string;

    /**
     * @param CacheBuilderInterface $builder
     * @param int $cacheBuilderType
     * @return array|null
     */
    public function readArray(CacheBuilderInterface $builder, int $cacheBuilderType): ?array;

    /**
     * @param CacheBuilderInterface $builder
     */
    public function invalidate(CacheBuilderInterface $builder): void;
}