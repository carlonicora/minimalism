<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DataInterface
{
    /**
     * @param string $dbReader
     * @return TableInterface
     */
    public function create(string $dbReader): TableInterface;
}