<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\helpers\apiResponse;
use carlonicora\minimalism\helpers\errorReporter;
use carlonicora\minimalism\helpers\security;

abstract class apiCaller {
    /** @var configurations */
    protected $configurations;

    /** @var int */
    public $httpCode;

    /** @var string */
    public $errorMessage;

    /** @var array */
    public $returnedValue;

    /** @var string */
    protected $verb;

    /** @var string */
    protected $uri;

    /** @var array */
    protected $body;

    public function __construct($configurations) {
        $this->configurations = $configurations;
    }

    protected function callAPI($verb, $uri, $body=null){
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
            default:
                $query = http_build_query($body);
                if (!empty($query)) $uri .= ((substr_count ( $uri, '?') > 0 ) ? '&' : '?') . $query;
                break;
        }

        if (!empty($this->configurations->getDebugKey())){
            $uri .= ((substr_count ( $uri, '?') > 0 ) ? '&' : '?') . 'XDEBUG_SESSION_START='.$this->configurations->getDebugKey();
        }

        $this->verb = $verb;
        $this->body = is_array($body) ? json_encode($body) : '';
        $this->uri = $uri;

        $security = new security($this->configurations);
        $signature = $security->generateSignature($verb, $uri, $body, $this->configurations->clientId, $this->configurations->clientSecret, $this->configurations->publicKey, $this->configurations->privateKey);
        $httpHeaders[] = 'Minimalism-Signature:' . $signature;

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

        $returnValue = new apiResponse();

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $returnedJson = substr($response, $header_size);

        $info = curl_getinfo($curl);

        if (isset($curl)) curl_close($curl);

        $returnValue->errorId = $info['http_code'];

        if ($returnValue->errorId != 200) {
            $returnValue->isSuccess = false;
            $returnValue->returnedValue = null;
            $returnValue->errorMessage = $returnedJson;

            $errorDescription = 'API call ' . $this->verb . ' ' . $this->uri . ' with parameters ' . json_encode($this->body) . ' returned error ' . $returnValue->errorId . ' ' . $returnValue->errorMessage;
            errorReporter::report($this->configurations, 20, $errorDescription);
        } else {
            $returnValue->isSuccess = true;
            $returnValue->returnedValue = json_decode($returnedJson, true);
        }

        return ($returnValue);
    }
}