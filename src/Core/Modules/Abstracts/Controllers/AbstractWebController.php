<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Events\MinimalismInfoEvents;
use Exception;
use JsonException;

abstract class AbstractWebController extends AbstractController
{
    /**
     *
     */
    public function preRender(): void
    {
        $this->initialiseView();
    }

    /**
     * @param int|null $code
     * @param string|null $response
     * @throws Exception
     */
    public function completeRender(int $code = null, string $response = null): void
    {
        try {
            $cookieValue = $this->services->serialiseCookies();
            setcookie('minimalismServices', $cookieValue, time() + (30 * 24 * 60 * 60));
        } catch (JsonException $e) {
            $this->services->logger()->error()
                ->log(MinimalismErrorEvents::COOKIE_SETTING_ERROR($e));
        }

        parent::completeRender($code, $response);

        $_SESSION['minimalismServices'] = $this->services;
        $this->services->logger()->info()->log(MinimalismInfoEvents::SESSION_PERSISTED());
    }

    /**
     *
     */
    abstract protected function initialiseView(): void;
}