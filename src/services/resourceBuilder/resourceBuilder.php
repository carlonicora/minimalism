<?php
namespace carlonicora\minimalism\services\resourceBuilder;

use carlonicora\minimalism\services\abstracts\abstractService;
use carlonicora\minimalism\services\resourceBuilder\interfaces\resourceBuilderInterface;

class resourceBuilder extends abstractService {
    /**
     * @param string $objectName
     * @param array $data
     * @return resourceBuilderInterface
     */
    public function create(string $objectName, array $data) : resourceBuilderInterface {
        return new $objectName($data);
    }
}