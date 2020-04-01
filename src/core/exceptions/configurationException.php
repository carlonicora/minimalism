<?php
namespace carlonicora\minimalism\core\exceptions;

use Exception;
use Throwable;

class configurationException extends Exception {
    public function __construct(string $serviceName, $message = '', $code = 0, Throwable $previous = null) {
        parent::__construct($serviceName . ' configuration error: ' . $message, $code, $previous);
    }
}
