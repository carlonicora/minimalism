<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DataLoaderInterface
{
    /**
     * UsersLoader constructor.
     * @param LoaderInterface $loader
     * @param DataInterface $data
     */
    public function __construct(
        LoaderInterface $loader,
        DataInterface $data,
    );
}