<?php /** @noinspection CurlSslServerSpoofingInspection */

namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\helpers\apiResponse;
use carlonicora\minimalism\helpers\errorReporter;
use carlonicora\minimalism\helpers\security;

abstract class abstractApiCaller {
    /** @var abstractConfigurations */
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

    /**
     * abstractApiCaller constructor.
     * @param $configurations
     */
    public function __construct($configurations) {
        $this->configurations = $configurations;
    }

    /**
     * @param string $verb
     * @param string $url
     * @param string $endpoint
     * @param array|null $body
     * @param string $hostname
     * @return apiResponse
     */
    protected function callAPI(string $verb,string  $url, string $endpoint, array $body=null, string $hostname=null): apiResponse {
        $curl = curl_init();
        $httpHeaders = array();

        if (!empty($hostname)){
            $httpHeaders[] = 'Host: ' . $hostname;
        }

        switch ($verb){
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                $httpHeaders[] = 'Content-Type:application/json';
                if (is_array($body)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body, JSON_THROW_ON_ERROR, 512));
                }
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                $httpHeaders[] = 'Content-Type:application/json';
                if (is_array($body)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body, JSON_THROW_ON_ERROR, 512));
                }
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                $httpHeaders[] = 'Content-Type:application/json';
                if (is_array($body)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body, JSON_THROW_ON_ERROR, 512));
                }
                break;
            default:
                if (isset($body)) {
                    $query = http_build_query($body);
                    if (!empty($query)) {
                        $endpoint .= ((substr_count($endpoint, '?') > 0) ? '&' : '?') . $query;
                    }

                    $body = null;
                }
                break;
        }

        if (!empty($this->configurations->getDebugKey())){
            $endpoint .= ((substr_count ( $endpoint, '?') > 0 ) ? '&' : '?') . 'XDEBUG_SESSION_START='.$this->configurations->getDebugKey();
        }

        $this->verb = $verb;
        $this->body = is_array($body) ? json_encode($body, JSON_THROW_ON_ERROR, 512) : '';
        $this->uri = $url . $endpoint;

        $security = new security($this->configurations);
        $signature = $security->generateSignature($verb, $endpoint, $body, $this->configurations->clientId, $this->configurations->clientSecret, $this->configurations->publicKey, $this->configurations->privateKey);
        $httpHeaders[] = $this->configurations->httpHeaderSignature . ':' . $signature;

        $info = null;
        $httpCode = null;

        $options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $httpHeaders,
            CURLOPT_URL => $url. $endpoint,
            CURLOPT_VERBOSE => 1,
            CURLOPT_HEADER => 1
        ];

        if ($this->configurations->allowUnsafeApiCalls){
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = false;
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);

        $returnValue = new apiResponse();

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $returnedJson = substr($response, $header_size);

        $info = curl_getinfo($curl);

        if (isset($curl)) {
            curl_close($curl);
        }

        $returnValue->errorId = $info['http_code'];

        if ($returnValue->errorId !== 200) {
            $returnValue->isSuccess = false;
            $returnValue->returnedValue = null;
            $returnValue->errorMessage = $returnedJson;

            $errorDescription = 'API call ' . $this->verb . ' ' . $this->uri . ' with parameters ' . json_encode($this->body, JSON_THROW_ON_ERROR, 512) . ' returned error ' . $returnValue->errorId . ' ' . $returnValue->errorMessage;
            errorReporter::report($this->configurations, 20, $errorDescription);
        } else {
            $returnValue->isSuccess = true;
            $returnValue->returnedValue = json_decode($returnedJson, true, 512, JSON_THROW_ON_ERROR);
        }

        return $returnValue;
    }
}