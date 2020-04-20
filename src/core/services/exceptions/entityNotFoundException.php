<?php
namespace carlonicora\minimalism\core\services\exceptions;

use RuntimeException;
use Throwable;

class entityNotFoundException extends RuntimeException {

    public function __construct($code, Throwable $previous = null) {
        parent::__construct('Not found', $code, $previous);
    }
}