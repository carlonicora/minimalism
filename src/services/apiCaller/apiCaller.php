<?php
namespace carlonicora\minimalism\services\apiCaller;

use carlonicora\minimalism\exceptions\serviceNotFoundException;
use carlonicora\minimalism\jsonapi\resources\errorObject;
use carlonicora\minimalism\jsonapi\resources\resourceObject;
use carlonicora\minimalism\jsonapi\responses\dataResponse;
use carlonicora\minimalism\services\apiCaller\configurations\apiCallerConfigurations;
use carlonicora\minimalism\services\factories\servicesFactory;
use carlonicora\minimalism\services\security\factories\serviceFactory;
use carlonicora\minimalism\services\security\security;

class apiCaller {
    /** @var apiCallerConfigurations */
    private apiCallerConfigurations $configData;

    /** @var servicesFactory  */
    private servicesFactory $services;

    /**
     * abstractApiCaller constructor.
     * @param apiCallerConfigurations $configData
     * @param servicesFactory $services
     */
    public function __construct(apiCallerConfigurations $configData, servicesFactory $services) {
        $this->configData = $configData;
        $this->services = $services;
    }

    /**
     * @param string $verb
     * @param string $url
     * @param string $endpoint
     * @param array|null $body
     * @param string $hostname
     * @return dataResponse
     * @throws serviceNotFoundException
     */
    public function call(string $verb, string $url, string $endpoint, array $body=null, string $hostname=null): dataResponse{
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

        /** @var security $security */
        $security = $this->services->service(serviceFactory::class);

        $signature = $security->generateSignature($verb, $endpoint, $body, $security->getClientId(), $security->getClientSecret(), $security->getPublicKey(), $security->getPrivateKey());
        $httpHeaders[] = $security->getHttpHeaderSignature() . ':' . $signature;

        $info = null;
        $httpCode = null;

        $options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $httpHeaders,
            CURLOPT_URL => $url. $endpoint,
            CURLOPT_VERBOSE => 1,
            CURLOPT_HEADER => 1
        ];

        if ($this->configData->allowUnsafeApiCalls){
            /** @noinspection CurlSslServerSpoofingInspection */
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            /** @noinspection CurlSslServerSpoofingInspection */
            $options[CURLOPT_SSL_VERIFYHOST] = false;
        }

        curl_setopt_array($curl, $options);

        $curlResponse = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $returnedJson = substr($curlResponse, $header_size);

        $info = curl_getinfo($curl);

        if (isset($curl)) {
            curl_close($curl);
        }

        $apiResponse = json_decode($returnedJson, true, 512, JSON_THROW_ON_ERROR);

        if (false === in_array($info['http_code'], [200, 201, 204], true)) {
            $response = new dataResponse();

            if (array_key_exists('errors', $apiResponse)){
                foreach ($apiResponse['errors'] as $error){
                    $response->addError(new errorObject($error));
                }
            }
        } else {
            $data = $apiResponse['data'];
            $response = new dataResponse($data);
        }

        if (array_key_exists('meta', $apiResponse)){
            $response->addMetas($apiResponse['meta']);
        }

        if (array_key_exists('links', $apiResponse)){
            $response->addLinks($apiResponse['links']);
        }

        if (array_key_exists('included', $apiResponse)){
            foreach ($apiResponse['included'] as $included){
                $response->addIncluded(new resourceObject($included));
            }
        }

        return $response;
    }

}