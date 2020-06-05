<?php
namespace CarloNicora\Minimalism\Core\Modules\Interfaces;

interface ResponseInterface
{
    /** @var string  */
    public const HTTP_STATUS_200='200';

    /** @var string  */
    public const HTTP_STATUS_201='201';

    /** @var string  */
    public const HTTP_STATUS_204='204';

    /** @var string  */
    public const HTTP_STATUS_205='205';

    /** @var string  */
    public const HTTP_STATUS_304='304';

    /** @var string  */
    public const HTTP_STATUS_400='400';

    /** @var string  */
    public const HTTP_STATUS_401='401';

    /** @var string  */
    public const HTTP_STATUS_403='403';

    /** @var string  */
    public const HTTP_STATUS_404='404';

    /** @var string  */
    public const HTTP_STATUS_405='405';

    /** @var string  */
    public const HTTP_STATUS_406='406';

    /** @var string  */
    public const HTTP_STATUS_409='409';

    /** @var string  */
    public const HTTP_STATUS_410='410';

    /** @var string  */
    public const HTTP_STATUS_411='411';

    /** @var string  */
    public const HTTP_STATUS_412='412';

    /** @var string  */
    public const HTTP_STATUS_415='415';

    /** @var string  */
    public const HTTP_STATUS_422='422';

    /** @var string  */
    public const HTTP_STATUS_428='428';

    /** @var string  */
    public const HTTP_STATUS_429='429';

    /** @var string  */
    public const HTTP_STATUS_500='500';

    /** @var string  */
    public const HTTP_STATUS_501='501';

    /** @var string  */
    public const HTTP_STATUS_502='502';

    /** @var string  */
    public const HTTP_STATUS_503='503';

    /** @var string  */
    public const HTTP_STATUS_504='504';

    /**
     * @return mixed
     */
    public function setNotHttpResponse();

    /**
     *
     */
    public function write() : void;

    /**
     *
     */
    public function writeContentType() : void;

    /**
     *
     */
    public function writeProtocol() : void;

    /**
     * @return string
     */
    public function getData() : string;

    /**
     * @param string $data
     */
    public function setData(string $data) : void;

    /**
     * @return string
     */
    public function getContentType() : string;

    /**
     * @param string $httpContentType
     */
    public function setContentType(string $httpContentType) : void;

    /**
     * @return string
     */
    public function getStatus() : string;

    /**
     * @param string $status
     */
    public function setStatus(string $status) : void;

    /**
     * @param string $modelName
     */
    public function setRedirect(string $modelName) : void;

    /**
     * @return string|null
     */
    public function redirects() : ?string;

    /**
     * @param array $parameters
     */
    public function setRedirectionParameters(array $parameters) : void;

    /**
     * @return array
     */
    public function getRedirectionParameters() : array;
}