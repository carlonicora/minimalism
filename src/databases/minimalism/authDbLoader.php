<?php
namespace carlonicora\minimalism\databases\minimalism;

use carlonicora\cryogen\dbLoader;
use Exception;

class authDbLoader extends dbLoader {
    public static function loadFromPublicKeyAndClientId($publicKey, $clientId){
        try {
            parent::init();
        } catch (Exception $e) {
        }

        self::$engine->setDiscriminant(auth::$field_publicKey, $publicKey);
        self::$engine->setDiscriminant(auth::$field_clientId, $clientId);

        try {
            return (parent::getSingle());
        } catch (Exception $e) {
            return(null);
        }
    }

    public static function deleteOldTokens(){
        try {
            parent::init();
        } catch (Exception $e) {
            return(null);
        }

        self::$engine->setDiscriminant(auth::$field_expirationDate, time(), "<");

        try {
            return (parent::delete(null, false));
        } catch (Exception $e) {
            return(null);
        }
    }
}