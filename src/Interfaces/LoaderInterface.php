<?php
namespace CarloNicora\Minimalism\Interfaces;

interface LoaderInterface
{
    /**
     * @return ServiceInterface|null
     */
    public function getDefaultService(): ?ServiceInterface;

    /**
     * @return CacheInterface|null
     */
    public function getCacher(): ?CacheInterface;
}