<?php
namespace CarloNicora\Minimalism;

use CarloNicora\Minimalism\Core\Bootstrapper;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
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
     * @param ModelInterface|string $model
     */
    public static function executeCli($model) : void
    {
        self::execute(CliController::class, $model);
    }

    /**
     * @param string $controllerClassName
     * @param ModelInterface|string|null $model
     */
    private static function execute(string $controllerClassName, $model=null) : void
    {
        $bootstrapper = new Bootstrapper();

        try {
            $redirect = null;
            $parameters = [];

            do {
                $response = $bootstrapper
                    ->initialise($controllerClassName)
                    ->loadController($model, $parameters)
                    ->render();

                if (($redirect = $response->redirects()) !== null){
                    $model = $redirect;
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
