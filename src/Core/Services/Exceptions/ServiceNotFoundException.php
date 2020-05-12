<?php
namespace CarloNicora\Minimalism\Core\Services\Exceptions;

use LogicException;
use Throwable;

class ServiceNotFoundException extends LogicException {
    /**
     * serviceNotFoundException constructor.
     * @param string $serviceName
     * @param null $code
     * @param Throwable|null $previous
     */
    public function __construct(string $serviceName, $code = null, Throwable $previous = null) {
        parent::__construct('Service ' . $serviceName . ' not found', $code, $previous);
    }
}