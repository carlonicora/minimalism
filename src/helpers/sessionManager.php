<?php
namespace carlonicora\minimalism\helpers;

use carlonicora\minimalism\abstracts\AbstractConfigurations;
use carlonicora\minimalism\library\database\databaseFactory;

class sessionManager
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @param AbstractConfigurations $configurations
     */
    public function loadFromSession(&$configurations)
    {
        if (isset($_SESSION['configurations'])){
            $configurations = $_SESSION['configurations'];
        } else {
            $configurations->loadConfigurations();
            if (isset($_COOKIE['campaign_builder_keys'])){
                list($configurations->publicKey, $configurations->privateKey) = explode(';', $_COOKIE['campaign_builder_keys']);
            }
        }

        databaseFactory::initialise($configurations);
    }

    /**
     * @param AbstractConfigurations $configurations
     */
    public function saveSession($configurations)
    {
        $_SESSION['configurations'] = $configurations;

        if (!empty($configurations->publicKey) && !empty($configurations->privateKey)){
            setcookie('campaign_builder_keys', $configurations->publicKey . ';' . $configurations->privateKey, time() + 30 * 24 * 60 * 60);
        }
    }

    public function destroySession()
    {
        unset($_SESSION["configurations"]);
        setcookie('campaign_builder_keys', '', time() - 24 * 60 * 60);
    }
}