<?php
namespace carlonicora\minimalism\databases\minimalism;

use Exception;

class clientsDbLoader extends dbLoader {
    public static function loadFromClientId($clientId){
        try {
            parent::init();
        } catch (Exception $e) {
            return(null);
        }

        self::$engine->setDiscriminant(clients::$field_clientId, $clientId);

        try {
            return (parent::getSingle());
        } catch (Exception $e) {
            return(null);
        }
    }
}