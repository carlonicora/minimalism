<?php
namespace carlonicora\minimalism\core\services\exceptions;

use LogicException;
use Throwable;

class serviceNotFoundException extends LogicException {
    /**
     * serviceNotFoundException constructor.
     * @param string $serviceName
     */
    public function __construct(string $serviceName, $code = null, Throwable $previous = null) {
        parent::__construct('Service ' . $serviceName . ' not found', $code, $previous);
    }
}