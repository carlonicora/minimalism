<?php
namespace carlonicora\minimalism\services\interfaces;

use carlonicora\minimalism\exceptions\configurationException;

interface serviceFactoryInterface {
    /**
     * @throws configurationException
     */
    public function __construct();

    /**
     * @return mixed
     */
    public function create();
}