<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\databases\minimalism\auth;
use carlonicora\minimalism\databases\minimalism\authDbLoader;
use carlonicora\minimalism\helpers\errorReporter;

abstract class functions {
    /** @var  configurations */
    protected $configurations;

    public function __construct($configurations){
        $this->configurations = $configurations;
    }

    public function validateAuth(){
        $returnValue = false;

        if (isset($this->configurations->token)) {
            /** @var auth $auth */
            $auth = authDbLoader::loadFromToken($this->configurations->token);

            if (isset($auth) && strtotime($auth->expirationDate) > time()) {
                $validToken = true;
                $returnValue = $auth->userId;
            }
        }

        if (!$validToken){
            errorReporter::report($this->configurations, 5, 'Invalid Token', 401);
        }

        return($returnValue);
    }
}