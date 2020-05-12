<?php
namespace CarloNicora\Minimalism\Core\Services\Exceptions;

use LogicException;
use Throwable;

class ConfigurationException extends LogicException {
    public const ERROR_CONFIGURATION_FILE_ERROR=1001;

    public function __construct(string $serviceName, $message = '', $code = 0, Throwable $previous = null) {
        parent::__construct($serviceName . ' configuration error: ' . $message, $code, $previous);
    }
}
