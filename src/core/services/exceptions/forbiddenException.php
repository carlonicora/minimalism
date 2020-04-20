<?php
namespace carlonicora\minimalism\core\services\exceptions;

use RuntimeException;
use Throwable;

class forbiddenException extends RuntimeException {

    public function __construct($code, Throwable $previous = null) {
        parent::__construct('Access denied', $code, $previous);
    }
}