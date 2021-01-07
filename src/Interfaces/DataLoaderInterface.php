<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DataLoaderInterface
{
    /**
     * UsersLoader constructor.
     * @param DataInterface $data
     * @param CacheInterface|null $cache
     * @param ServiceInterface|null $service
     */
    public function __construct(
        DataInterface $data,
        ?CacheInterface $cache,
        ?ServiceInterface $service,
    );
}