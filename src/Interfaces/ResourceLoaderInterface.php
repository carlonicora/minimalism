<?php
namespace CarloNicora\Minimalism\Interfaces;

interface ResourceLoaderInterface
{
    /**
     * UsersLoader constructor.
     * @param ServiceInterface $jsonApi
     * @param CacheInterface|null $cache
     * @param ServiceInterface|null $service
     */
    public function __construct(
        ServiceInterface $jsonApi,
        ?CacheInterface $cache,
        ?ServiceInterface $service,
    );
}