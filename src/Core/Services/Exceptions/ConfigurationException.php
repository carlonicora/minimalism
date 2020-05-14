<?php
namespace CarloNicora\Minimalism\Core\Services\Exceptions;

use LogicException;
use Throwable;

class ConfigurationException extends LogicException {
    public const ERROR_CONFIGURATION_FILE_ERROR=1001;
    public const ERROR_INVALID_COOKIE_VALUE=1002;
    public const ERROR_NO_MODULE_AVAILABLE=1003;
    public const ERROR_MULITPLE_MODULES_AVAILABLE=1004;

    public function __construct(string $serviceName, $message = '', $code = 0, Throwable $previous = null) {
        parent::__construct($serviceName . ' configuration error: ' . $message, $code, $previous);
    }
}
