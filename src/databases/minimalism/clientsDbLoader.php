<?php
namespace carlonicora\minimalism\databases\minimalism;

use carlonicora\minimalism\abstracts\databaseLoader;

class clientsDbLoader extends databaseLoader {
    public function loadFromClientId($clientId){
        $this->engine->setDiscriminant(clients::$field_clientId, $clientId);

        $response = $this->getSingle();

        return($response);
    }
}