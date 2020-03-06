<?php
namespace carlonicora\minimalism\helpers;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\factories\databaseFactory;

class sessionManager {
    /**
     * sessionManager constructor.
     */
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @param abstractConfigurations $configurations
     */
    public function loadFromSession(abstractConfigurations &$configurations): void {
        if (isset($_SESSION['configurations'])){
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $configurations = $_SESSION['configurations'];
            $configurations->cleanNonPersistentVariables();
        } else {
            $configurations->loadConfigurations();
            if (isset($_COOKIE['minimalismConfigurations'])){
                $configurations->unserialiseCookies($_COOKIE['minimalismConfigurations']);
            }
        }

        databaseFactory::initialise($configurations);
    }

    /**
     * @param abstractConfigurations $configurations
     */
    public function saveSession($configurations): void {
        $configurations->cleanNonPersistentVariables();
        $_SESSION['configurations'] = $configurations;
        setcookie('minimalismConfigurations', $configurations->serialiseCookies(), time() + (30 * 24 * 60 * 60));
    }

    /**
     *
     */
    public function destroySession(): void {
        unset($_SESSION['configurations']);
        setcookie('minimalismConfigurations', '', time() - 24 * 60 * 60);
    }
}