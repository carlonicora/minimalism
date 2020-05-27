<?php
namespace CarloNicora\Minimalism\Core;

use Exception;

class Minimalism
{
    /**
     *
     */
    public static function execute() : void
    {
        $bootstrapper = new Bootstrapper();

        try {
            $bootstrapper
                ->initialise()
                ->loadController()
                ->render()
                ->write();
        } catch (Exception $e) {
            http_response_code((int)$e->getCode());
            $GLOBALS['http_response_code'] = $e->getCode();
            echo $e->getMessage();
        }
    }
}