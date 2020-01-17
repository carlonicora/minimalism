<?php
namespace carlonicora\minimalism\helpers;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\interfaces\securityClientInterface;
use carlonicora\minimalism\interfaces\securitySessionInterface;
use Exception;

class security {
    /** @var abstractConfigurations */
    private $configurations;

    /**
     * security constructor.
     * @param $configurations
     */
    public function __construct($configurations) {
        $this->configurations = $configurations;
    }

    /**
     * @param $verb
     * @param $uri
     * @param $body
     * @param $clientId
     * @param $clientSecret
     * @param $publicKey
     * @param $privateKey
     * @param null $time
     * @return string|null
     */
    public function generateSignature($verb, $uri, $body, $clientId, $clientSecret, $publicKey, $privateKey, $time=null): ?string {
        $returnValue = null;

        if (empty($time)) {
            $time = time();
        }

        $strings = array($verb, $uri, $time);
        if (isset($body) && count($body)) {
            $body_json = json_encode($body, JSON_THROW_ON_ERROR, 512);
            $strings[] = md5($body_json);
        }
        if (isset($privateKey)) {
            $strings[] = $privateKey;
        }

        $checksum = hash_hmac('SHA256', implode("\n", $strings), $clientSecret);

        $sessionPublicKey = empty($publicKey) ? '' : $publicKey;
        $returnValue = $clientId . $sessionPublicKey . $time . $checksum;

        return $returnValue;
    }

    /**
     * @param $signature
     * @param $verb
     * @param $uri
     * @param $body
     * @param securityClientInterface $client
     * @param securitySessionInterface $session
     * @return bool
     */
    public function validateSignature($signature, $verb, $uri, $body, securityClientInterface $client, securitySessionInterface $session): bool {
        if (empty($signature)) {
            return false;
        }

        $this->configurations->clientId = '';
        $this->configurations->publicKey = '';
        $time = null;

        if (strlen($signature) === 202){
            $this->configurations->clientId = substr($signature, 0, 64);
            $this->configurations->publicKey = substr($signature, 64, 64);
            $time = substr($signature, 128, 10);
        } elseif (strlen($signature) === 138){
            $this->configurations->clientId = substr($signature, 0, 64);
            $time = substr($signature, 64, 10);
        } else {
            return false;
        }

        $timeNow = time();
        $timeDifference = $timeNow - $time;

        if ($timeDifference > 10 || $timeDifference < -10) {
            errorReporter::report($this->configurations, 9, 'Request time: ' . $time . ' - Time now: ' . $timeNow . ' - Time difference: ' . $timeDifference, 408);
        }

        try {
            $this->configurations->clientSecret = $client->getSecret($this->configurations->clientId);
        } catch (Exception $e) {
            errorReporter::report($this->configurations, 10, null, 401);
        }

        $this->configurations->privateKey=null;

        $auth = null;
        if (!empty($this->configurations->publicKey)){
            try {
                $this->configurations->privateKey = $session->getPrivateKey($this->configurations->publicKey, $this->configurations->clientId);
            } catch (Exception $e) {
                if ($e->getCode() === 2){
                    errorReporter::report($this->configurations, 11, 'Expired', 401);
                } else {
                    errorReporter::report($this->configurations, 11, null, 401);
                }
            }
        }

        $validatedSignature = $this->generateSignature($verb, $uri, $body, $this->configurations->clientId, $this->configurations->clientSecret, $this->configurations->publicKey, $this->configurations->privateKey, $time);

        return $validatedSignature === $signature;
    }

    /**
     * Encrypts a string in order to generate a password
     *
     * @param string $password
     * @return string
     */
    public static function encryptPassword($password): string {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verifies if a password matches its hash
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function decryptPassword($password, $hash): bool {
        $returnValue = false;

        if (password_verify($password, $hash)){
            $returnValue = true;
        }

        return $returnValue;
    }

    /**
     * Generates a pair of public and private keys
     *
     * @param $publicKey
     * @param $privateKey
     */
    public static function generateApiKeys(&$publicKey, &$privateKey): void {
        try {
            $publicKey = bin2hex(random_bytes(32));
            $privateKey = bin2hex(random_bytes(64));
        } catch (Exception $e){
            $publicKey = null;
            $privateKey = null;
        }
    }
}