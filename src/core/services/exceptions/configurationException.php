<?php
namespace carlonicora\minimalism\core\services\exceptions;

use LogicException;
use Throwable;

class configurationException extends LogicException {
    public function __construct(string $serviceName, $message = '', $code = 0, Throwable $previous = null) {
        parent::__construct($serviceName . ' configuration error: ' . $message, $code, $previous);
    }
}
