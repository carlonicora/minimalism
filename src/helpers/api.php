<?php
namespace carlonicora\minimalism\helpers;

use carlonicora\minimalism\abstracts\configurations;

class api {
    /** @var configurations */
    private $configurations;

    /** @var int */
    private $httpCode;

    /** @var string */
    private $errorMessage;

    public function __construct($configurations) {
        $this->configurations = $configurations;
    }

    public function callAPI($verb, $uri, $body=null){
        if (!empty($this->configurations->getDebugKey())){
            $uri .= ((substr_count ( $uri, '?') > 0 ) ? '&' : '?') . 'XDEBUG_SESSION_START='.$this->configurations->getDebugKey();
        }

        $curl = curl_init();
        $httpHeaders = array();

        switch ($verb){
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                $httpHeaders[] = 'Content-Type:application/json';
                if (is_array($body)) curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                $httpHeaders[] = 'Content-Type:application/json';
                if (is_array($body)) curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }

        $security = new security($this->configurations);
        $signature = $security->generateSignature($verb, $uri, $body, $this->configurations->clientId, $this->configurations->clientSecret, $this->configurations->publicKey, $this->configurations->privateKey);
        $httpHeaders[] = 'minimalism-signature:' . $signature;

        $info = null;
        $httpCode = null;

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $httpHeaders,
            CURLOPT_URL => $uri,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => 1,
            CURLOPT_HEADER => 1
        ));

        $response = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $returnValue = substr($response, $header_size);

        $info = curl_getinfo($curl);

        if (isset($curl)) curl_close($curl);

        $this->httpCode = $info['http_code'];

        if ($this->httpCode != 200) {
            $this->errorMessage = $returnValue;
            $returnValue = false;
        }

        return ($returnValue);
    }
}