<?php

namespace CarloNicora\Minimalism\Interfaces;

interface CoreInterface
{
    // Objects which implements this interface, should be loaded and cached on each request, not waiting for the first initalisation
    // Examples - Path, Logger, Cache, MySql
}