<?php
namespace carlonicora\minimalism\databases\minimalism;

use carlonicora\cryogen\dbLoader;

class authDbLoader extends dbLoader {
    public static function loadFromPublicKeyAndClientId($publicKey, $clientId){
        parent::init();

        self::$engine->setDiscriminant(auth::$field_publicKey, $publicKey);
        self::$engine->setDiscriminant(auth::$field_clientId, $clientId);

        return(parent::getSingle());
    }

    public static function deleteOldTokens(){
        parent::init();

        self::$engine->setDiscriminant(auth::$field_expirationDate, time(), "<");

        return(parent::delete(null, false));
    }
}