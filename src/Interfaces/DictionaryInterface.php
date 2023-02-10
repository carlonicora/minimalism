<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DictionaryInterface
{
    /**
     * @return string
     */
    public function getEndpoint(
    ): string;

    public function getIdKey(
    ): string;

    public function getPlural(
    ): string;

    public function getTableClass(
    ): string;
}