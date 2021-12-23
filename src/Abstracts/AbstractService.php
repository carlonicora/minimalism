<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;

abstract class AbstractService implements ServiceInterface
{
    /** @var ObjectFactory|null  */
    protected ?ObjectFactory $objectFactory;

    /**
     *
     */
    public function __construct()
    {
        $this->initialise();
    }

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
        $this->objectFactory = null;
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
     * @return string|null
     */
    public static function getBaseInterface(
    ): ?string
    {
        return null;
    }
}