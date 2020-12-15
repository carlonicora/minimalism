<?php
namespace CarloNicora\Minimalism;

use CarloNicora\Minimalism\Core\Bootstrapper;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Services\Exceptions\MinimalismHttpException;
use CarloNicora\Minimalism\Modules\Api\ApiController;
use CarloNicora\Minimalism\Modules\Cli\CliController;
use CarloNicora\Minimalism\Modules\Web\WebController;
use Exception;

class Minimalism
{
    public static function executeWeb() : void
    {
        self::execute(WebController::class);
    }

    public static function executeApi() : void
    {
        header("Access-Control-Allow-Origin: *");
        if (false === empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, referrer");
            header("Access-Control-Allow-Methods: OPTIONS, GET, POST, DELETE, PUT");
            header("Allow: OPTIONS, GET, POST, DELETE, PUT");
            http_response_code(200);
            echo(0);
            exit;
        }

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
        /** @var Bootstrapper $bootstrapper */
        $bootstrapper = null;

        try {
            $bootstrapper = new Bootstrapper();

            $redirect = null;
            $parameters = [];

            do {
                $response = $bootstrapper
                    ->initialise($controllerClassName)
                    ->loadController($model, $parameters)
                    ->render();

                if (($redirect = $response->redirects()) !== null) {
                    $model = $redirect;
                    $parameters = $response->getRedirectionParameters();
                }
            } while ($redirect !== null);

            $response->write();
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (MinimalismHttpException $e) {
            http_response_code((int)$e->getHttpStatusCode());
            $GLOBALS['http_response_code'] = $e->getHttpStatusCode();
            echo $e->getMessage();

            $bootstrapper->saveException($e);
        } catch (Exception $e) {
            http_response_code((int)$e->getCode());
            $GLOBALS['http_response_code'] = $e->getCode();
            echo $e->getMessage();

            $bootstrapper->saveException($e);
        }
    }
}
