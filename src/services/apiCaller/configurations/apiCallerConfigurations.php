<?php
namespace carlonicora\minimalism\services\apiCaller\configurations;

class apiCallerConfigurations {
    /** @var bool */
    public bool $allowUnsafeApiCalls;

    /**
     * apiCallerConfigurations constructor.
     */
    public function __construct() {
        $this->allowUnsafeApiCalls = getenv('ALLOW_UNSAFE_API_CALLS') ?: false;
    }
}