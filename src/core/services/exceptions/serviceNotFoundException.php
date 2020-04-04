<?php
namespace carlonicora\minimalism\core\services\exceptions;

use Exception;

class serviceNotFoundException extends Exception {
    /**
     * serviceNotFoundException constructor.
     * @param string $serviceName
     */
    public function __construct(string $serviceName) {
        parent::__construct('Service ' . $serviceName . ' not found');
    }
}