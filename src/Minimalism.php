<?php
namespace CarloNicora\Minimalism;

use CarloNicora\Minimalism\Factories\ModelFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use Composer\InstalledVersions;
use Exception;
use PackageVersions\Versions;
use Throwable;

class Minimalism
{
    /** @var ServiceFactory  */
    private ServiceFactory $services;

    /**
     * Minimalism constructor.
     */
    public function __construct()
    {
        $this->services = new ServiceFactory();
    }

    /**
     * @param string|null $modelName
     * @return string
     */
    public function render(?string $modelName=null): string
    {
        header('X-Powered-By: minimalism/' . InstalledVersions::getPrettyVersion(Versions::rootPackageName()));
        $modelFactory = new ModelFactory($this->services);

        $model = null;
        $data = null;

        try {
            $model = $modelFactory->create($modelName);
            $httpResponse = $model->run();
            $data = $model->getDocument();
            $response = $data->export();
        } catch (Exception $e) {
            $httpResponse = $e->getCode() ?? 500;
            $response = $e->getMessage();
        }

        if ($response === '{"meta":[]}'){
            $response = '';
        }

        header('Content-Type: application/vnd.api+json');

        if ($model !== null && ($transformer = $this->services->getTransformer()) !== null && ($view = $model->getView()) !== null){
            header('Content-Type: ' . $transformer->getContentType());
            try {
                $response = $transformer->transform(
                    $data,
                    $view
                );
            } catch (Exception| Throwable) {
                $httpResponse = 500;
                $response = 'Error transforming the view';
            }
        }

        header($this->getProtocol() . ' ' . $httpResponse . ' ' . $this->httpCodes[$httpResponse]);

        return $response ?? '';
    }

    /**
     * @return string
     */
    private function getProtocol() : string
    {
        return ($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1');
    }

    /** @var string[]  */
    private array $httpCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];
}