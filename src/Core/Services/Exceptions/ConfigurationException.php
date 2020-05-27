<?php
namespace CarloNicora\Minimalism\Core\Services\Exceptions;

use LogicException;
use Throwable;

class ConfigurationException extends LogicException
{
    /**
     * ConfigurationException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct('Configuration error (' . $message . ')', $code, $previous);
    }
}
