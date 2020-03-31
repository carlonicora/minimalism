<?php
namespace carlonicora\minimalism\services\resourceBuilder\interfaces;

use carlonicora\minimalism\jsonapi\resources\resourceObject;

interface resourceBuilderInterface {
    /**
     * @return resourceObject
     */
    public function buildResource(): resourceObject;
}