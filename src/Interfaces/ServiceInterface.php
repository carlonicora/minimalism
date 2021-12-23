<?php
namespace CarloNicora\Minimalism\Interfaces;

use CarloNicora\Minimalism\Factories\ObjectFactory;

interface ServiceInterface
{
    /**
     * The initialise method should contain all the functions that needs to
     * be run if the service is loaded from cache.
     * This can include reading user-specific parameters from the session
     */
    public function initialise(): void;

    /**
     * The destroy method should contain all the functions that needs to
     * be run before serialising the service to cache.
     * This should include the removal of all the user-specific parameters
     * from the service and add them to the session
     */
    public function destroy(): void;

    /**
     * @param ObjectFactory $objectFactory
     * @return void
     */
    public function setObjectFactory(ObjectFactory $objectFactory): void;

    /**
     * Returns the base interface (if any) the service interface extends.
     * When a service extends a base interface, an appliation can contain
     * only one service that extends that interface.
     * @return string|null
     */
    public static function getBaseInterface(): ?string;
}