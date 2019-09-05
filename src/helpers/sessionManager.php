<?php /** @noinspection ALL */

namespace carlonicora\minimalism\helpers;

use carlonicora\minimalism\abstracts\AbstractConfigurations;
use carlonicora\minimalism\library\database\databaseFactory;

class sessionManager {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @param AbstractConfigurations $configurations
     */
    public function loadFromSession(&$configurations): void
    {
        if (isset($_SESSION['configurations'])){
            $configurations = $_SESSION['configurations'];
        } else {
            $configurations->loadConfigurations();
            if (isset($_COOKIE['minimalismConfigurations'])){
                $configurations->unserialiseCookies($_COOKIE['minimalismConfigurations']);
            }
        }

        databaseFactory::initialise($configurations);
    }

    /**
     * @param AbstractConfigurations $configurations
     */
    public function saveSession($configurations): void
    {
        $_SESSION['configurations'] = $configurations;
        setcookie('minimalismConfigurations', $configurations->serialiseCookies(), time() + (30 * 24 * 60 * 60));
    }

    public function destroySession(): void
    {
        unset($_SESSION['configurations']);
        setcookie('minimalismConfigurations', '', time() - 24 * 60 * 60);
    }
}