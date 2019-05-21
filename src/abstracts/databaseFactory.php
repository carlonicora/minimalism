<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\databases\minimalism\authDbLoader;
use carlonicora\minimalism\databases\minimalism\clientsDbLoader;

class databaseFactory {
    protected static $configurations;

    public static function initialise($configurations){
        self::$configurations = $configurations;
    }

    /**
     * @return authDbLoader
     */
    public static function getAuthDbLoader(){
        $response = new authDbLoader(self::$configurations);

        return($response);
    }

    /**
     * @return clientsDbLoader
     */
    public static function getClientsDbLoader(){
        $response = new clientsDbLoader(self::$configurations);

        return($response);
    }
}