<?php

namespace CarloNicora\Minimalism\Interfaces;

/**
 * To separate services, which requires initialisation.
 */
interface InitialisableInterface
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
}