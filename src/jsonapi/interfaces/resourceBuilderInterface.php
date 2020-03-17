<?php
namespace carlonicora\minimalism\jsonapi\interfaces;

use carlonicora\minimalism\jsonapi\resources\resourceObject;

interface resourceBuilderInterface {
    /**
     * @return resourceObject
     */
    public function buildResource(): resourceObject;
}