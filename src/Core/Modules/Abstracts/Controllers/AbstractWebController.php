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
            $this->setCookie('minimalismServices', $cookieValue, (30 * 24 * 60 * 60));
        } catch (JsonException $e) {
            $this->services->logger()->error()
                ->log(MinimalismErrorEvents::COOKIE_SETTING_ERROR($e));
        }

        parent::completeRender($code, $response);

        $_SESSION['minimalismServices'] = $this->services;
        $this->services->logger()->info()->log(MinimalismInfoEvents::SESSION_PERSISTED());
    }

    /**
     * Helper method that allows us to unit test completeRender
     * @param string $name
     * @param string $value
     * @param int $lifetime
     */
    protected function setCookie(string $name, string $value, int $lifetime): void
    {
        \setcookie($name, $value, time() + $lifetime);
    }

    /**
     *
     */
    abstract protected function initialiseView(): void;
}
