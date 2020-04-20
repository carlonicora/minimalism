<?php
namespace carlonicora\minimalism\core\services\exceptions;

use RuntimeException;
use Throwable;

class unauthorizedException extends RuntimeException {

    public function __construct($code, Throwable $previous = null) {
        parent::__construct('Unauthorized', $code, $previous);
    }
}