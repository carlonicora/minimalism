<?php
namespace carlonicora\minimalism\jsonapi\interfaces;

use carlonicora\minimalism\jsonapi\resources\resourceObject;

interface resourceBuilderInterface {

    /**
     * @param array $data
     * @return resourceObject
     */
    public function buildResource(array $data): resourceObject;

}