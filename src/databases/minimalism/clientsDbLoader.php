<?php
namespace carlonicora\minimalism\databases\minimalism;

use carlonicora\cryogen\dbLoader;

class clientsDbLoader extends dbLoader {
    public static function loadFromClientId($clientId){
        parent::init();

        self::$engine->setDiscriminant(clients::$field_clientId, $clientId);

        return(parent::getSingle());
    }
}