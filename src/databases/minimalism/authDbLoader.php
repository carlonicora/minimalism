<?php
namespace carlonicora\minimalism\databases\minimalism;

use carlonicora\minimalism\helpers\databaseLoader;

class authDbLoader extends databaseLoader {

    public function loadFromPublicKeyAndClientId($publicKey, $clientId){
        $this->engine->setDiscriminant(auth::$field_publicKey, $publicKey);
        $this->engine->setDiscriminant(auth::$field_clientId, $clientId);

        $response = $this->getSingle();

        return($response);
    }

    public function deleteOldTokens(){
        $this->engine->setDiscriminant(auth::$field_expirationDate, time(), "<");

        $response = $this->delete(null, false);

        return($response);
    }
}