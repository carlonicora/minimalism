<?php
namespace CarloNicora\Minimalism;

use CarloNicora\Minimalism\Core\Bootstrapper;
use CarloNicora\Minimalism\Modules\Api\ApiController;
use CarloNicora\Minimalism\Modules\Cli\CliController;
use CarloNicora\Minimalism\Modules\Web\WebController;
use Exception;

class Minimalism
{
    /**
     *
     */
    public static function executeWeb() : void
    {
        self::execute(WebController::class);
    }

    /**
     *
     */
    public static function executeApi() : void
    {
        self::execute(ApiController::class);
    }

    /**
     * @param string $modelName
     */
    public static function executeCli(string $modelName) : void
    {
        self::execute(CliController::class, $modelName);
    }

    /**
     * @param string $controllerClassName
     * @param string|null $modelName
     */
    private static function execute(string $controllerClassName, string $modelName=null) : void
    {
        $bootstrapper = new Bootstrapper();

        try {
            $redirect = null;
            $parameters = [];

            do {
                $response = $bootstrapper
                    ->initialise($controllerClassName)
                    ->loadController($modelName, $parameters)
                    ->render();

                if (($redirect = $response->redirects()) !== null){
                    $modelName = $redirect;
                    $parameters = $response->getRedirectionParameters();
                }
            } while ($redirect !== null);

            $response->write();
        } catch (Exception $e) {
            http_response_code((int)$e->getCode());
            $GLOBALS['http_response_code'] = $e->getCode();
            echo $e->getMessage();
        }
    }
}