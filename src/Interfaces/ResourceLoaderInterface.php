<?php
namespace CarloNicora\Minimalism\Interfaces;

interface ResourceLoaderInterface
{
    /**
     * UsersLoader constructor.
     * @param LoaderInterface $loader
     * @param ServiceInterface $data
     */
    public function __construct(
        LoaderInterface $loader,
        ServiceInterface $data,
    );
}