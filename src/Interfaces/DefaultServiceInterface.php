<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DefaultServiceInterface
{
    /**
     * @return LoaderInterface|null
     */
    public function getLoaderInterface(): ?LoaderInterface;
}