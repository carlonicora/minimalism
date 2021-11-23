<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\Minimalism\Interfaces\ServiceInterface;

abstract class AbstractService implements ServiceInterface
{
    /**
     *
     */
    public function initialise(
    ): void
    {
    }

    /**
     *
     */
    public function destroy(
    ): void
    {
    }

    /**
     * @return string|null
     */
    public static function getBaseInterface(
    ): ?string
    {
        return null;
    }
}