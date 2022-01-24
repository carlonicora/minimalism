<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;

abstract class AbstractService implements ServiceInterface
{
    /** @var ObjectFactory|null  */
    protected ?ObjectFactory $objectFactory;

    /**
     *
     */
    public function initialise(
    ): void
    {
    }

    /**
     * @param ServiceFactory $services
     * @return void
     */
    public function postIntialise(
        ServiceFactory $services,
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
     * @param ObjectFactory $objectFactory
     * @return void
     */
    final public function setObjectFactory(
        ObjectFactory $objectFactory,
    ): void
    {
        $this->objectFactory = $objectFactory;
    }

    /**
     * @return void
     */
    final public function unsetObjectFactory(
    ): void
    {
        $this->objectFactory = null;
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