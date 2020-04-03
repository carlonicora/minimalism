<?php
namespace carlonicora\minimalism\services\security;

use carlonicora\minimalism\core\services\abstracts\abstractService;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceConfigurationsInterface;
use carlonicora\minimalism\services\security\configurations\securityConfigurations;
use carlonicora\minimalism\services\security\interfaces\securityClientInterface;
use carlonicora\minimalism\services\security\interfaces\securitySessionInterface;
use Exception;
use RuntimeException;

class security extends abstractService {
    /** @var securityConfigurations  */
    private securityConfigurations $configData;

    /**
     * abstractApiCaller constructor.
     * @param serviceConfigurationsInterface $configData
     * @param servicesFactory $services
     */
    public function __construct(serviceConfigurationsInterface $configData, servicesFactory $services) {
        parent::__construct($configData, $services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->configData = $configData;
    }

    /**
     * @return string
     */
    public function getHttpHeaderSignature() : string {
        return $this->configData->httpHeaderSignature;
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
            $body_json = json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES, 512);
            $strings[] = md5($body_json);
        }
        if (!empty($privateKey)) {
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
     */
    public function validateSignature($signature, $verb, $uri, $body, securityClientInterface $client, securitySessionInterface $session): void {
        if (empty($signature)) {
            throw new RuntimeException('Security violation: missing signature', 401);
        }

        $this->configData->clientId = '';
        $this->configData->publicKey = '';
        $time = null;

        if (strlen($signature) === 202){
            $this->configData->clientId = substr($signature, 0, 64);
            $this->configData->publicKey = substr($signature, 64, 64);
            $time = substr($signature, 128, 10);
        } elseif (strlen($signature) === 138){
            $this->configData->clientId = substr($signature, 0, 64);
            $time = substr($signature, 64, 10);
        } else {
            throw new RuntimeException('Security violation: signature structure error', 401);
        }

        $timeNow = time();
        $timeDifference = $timeNow - $time;

        if ($timeDifference > 10 || $timeDifference < -10) {
            throw new RuntimeException('Security violation: signature expired', 401);
        }

        try {
            $this->configData->clientSecret = $client->getSecret($this->configData->clientId);
        } catch (Exception $e) {
            throw new RuntimeException('Security violation: invalid client id', 401);
        }

        $this->configData->privateKey=null;

        $auth = null;
        if (!empty($this->configData->publicKey)){
            try {
                $this->configData->privateKey = $session->getPrivateKey($this->configData->publicKey, $this->configData->clientId);
            } catch (Exception $e) {
                if ($e->getCode() === 2){
                    throw new RuntimeException('Security violation: session expired', 401);
                }

                throw new RuntimeException('Security violation: session not found', 401);
            }
        }

        if ($verb === 'GET'){
            $body = null;
        }

        $validatedSignature = $this->generateSignature($verb, $uri, $body, $this->configData->clientId, $this->configData->clientSecret, $this->configData->publicKey, $this->configData->privateKey, $time);

        if($validatedSignature !== $signature){
            throw new RuntimeException('Security violation: signature error', 401);
        }
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

    /**
     * @return securityClientInterface
     */
    public function getSecurityClient(): securityClientInterface {
        return $this->configData->securityClient;
    }

    /**
     * @return securitySessionInterface
     */
    public function getSecuritySession(): securitySessionInterface {
        return $this->configData->securitySession;
    }

    /**
     * @param securityClientInterface $securityClient
     */
    public function setSecurityClient(securityClientInterface $securityClient): void {
        $this->configData->securityClient = $securityClient;
    }

    /**
     * @param securitySessionInterface $securitySession
     */
    public function setSecuritySession(securitySessionInterface $securitySession): void {
        $this->configData->securitySession = $securitySession;
    }

    /**
     * @return string
     */
    public function getClientId(): string {
        return $this->configData->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string {
        return $this->configData->clientSecret;
    }

    /**
     * @return string
     */
    public function getPublicKey(): string {
        return $this->configData->publicKey;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string {
        return $this->configData->privateKey;
    }

    /**
     * @param string|null $clientId
     */
    public function setClientId(?string $clientId): void {
        $this->configData->clientId = $clientId;
    }

    /**
     * @param string|null $clientSecret
     */
    public function setClientSecret(?string $clientSecret): void {
        $this->configData->clientSecret = $clientSecret;
    }

    /**
     * @param string|null $publicKey
     */
    public function setPublicKey(?string $publicKey): void {
        $this->configData->publicKey = $publicKey;
    }

    /**
     * @param string|null $privateKey
     */
    public function setPrivateKey(?string $privateKey): void {
        $this->configData->privateKey = $privateKey;
    }
}