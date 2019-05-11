<?php
namespace carlonicora\minimalism\helpers;

use carlonicora\minimalism\abstracts\configurations;
use carlonicora\minimalism\databases\minimalism\auth;
use carlonicora\minimalism\databases\minimalism\authDbLoader;
use carlonicora\minimalism\databases\minimalism\clients;
use carlonicora\minimalism\databases\minimalism\clientsDbLoader;

class security {
    /** @var configurations */
    private $configurations;

    public function __construct($configurations) {
        $this->configurations = $configurations;
    }

    public function generateSignature($verb, $uri, $body, $clientId, $clientSecret, $publicKey, $privateKey, $time=null){
        $returnValue = null;

        if (empty($time)) {
            $time = time();
        }

        $strings = array($verb, $uri, $time);
        if (isset($body) && count($body)) {
            $body_json = json_encode($body);
            array_push($strings, md5($body_json));
        }
        if (isset($privateKey)) {
            array_push($strings, $privateKey);
        }

        $checksum = hash_hmac('SHA256', join("\n", $strings), $clientSecret, false);

        $sessionPublicKey = empty($publicKey) ? '' : $publicKey;
        $returnValue = $clientId . $sessionPublicKey . $time . $checksum;

        return($returnValue);
    }

    public function validateSignature($signature, $verb, $uri, $body){
        $clientId = '';
        $publicKey = '';
        $time = null;

        if (strlen($signature) == 138){
            $clientId = substr($signature, 0, 32);
            $publicKey = substr($signature, 32, 32);
            $time = substr($signature, 64, 10);
        } elseif (strlen($signature) == 106){
            $clientId = substr($signature, 0, 32);
            $time = substr($signature, 32, 10);
        }

        $timeDifference = time() - $time;

        if ($timeDifference > 10 || $timeDifference < 0) errorReporter::report($this->configurations, 9, null, 408);

        /** @var clients $client */
        $client = clientsDbLoader::loadFromClientId($clientId);

        if (empty($client)) errorReporter::report($this->configurations, 10, null, 401);

        /** @var auth $auth */
        $auth = authDbLoader::loadFromPublicKeyAndClientId($publicKey, $client->id);
        if (empty($auth)) errorReporter::report($this->configurations, 11, null, 401);
        if (time() > strtotime($auth->expirationDate) ) errorReporter::report($this->configurations, 11, 'Expired', 401);

        $validatedSignature = $this->generateSignature($verb, $uri, $body, $clientId, $client->clientSecret, $publicKey, $auth->privateKey, $time);

        $returnValue = $validatedSignature == $signature;

        return($returnValue);
    }
}