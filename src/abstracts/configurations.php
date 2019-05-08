<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\cryogen\connectionBuilder;
use carlonicora\cryogen\cryogen;
use carlonicora\cryogen\cryogenBuilder;
use Dotenv\Dotenv;
use carlonicora\minimalism\helpers\errorReporter;

abstract class configurations{
    const MINIMALISM_APP = 1;
    const MINIMALISM_API = 2;

    /** @var string $namespace */
    private $namespace;

    /** @var string $rootDirectory */
    private $rootDirectory;

    /** @var string */
    public $appDirectory;

    /** @var Dotenv $env */
    protected $env;

    /** @var string $baseUrl */
    private $baseUrl;

    /** @var string */
    protected $debugKey;

    /** @var array */
    public $cryogen = array();

    /** @var array */
    public $cryogenConnections = array();

    /** @var int */
    public $applicationType;

    /** @var string */
    public $privateKey;

    /** @var string */
    public $publicKey;

    /** @var string */
    public $clientId;

    /** @var string */
    public $clientSecret;

    public function __construct($namespace){
        $child = get_called_class();
        $class_info = new \ReflectionClass($child);
        $this->appDirectory = dirname($class_info->getFileName());

        $this->namespace = $namespace;
        $this->rootDirectory = $_SERVER["DOCUMENT_ROOT"];

        $headers = getallheaders();
        $bearer = isset($headers["Authorization"]) ? $headers["Authorization"] : null;
        if (substr($bearer, 0, 7) == 'Bearer ') {
            $this->token = substr($bearer, 7);
        } else {
            $this->token = null;
        }
    }

    public function loadConfigurations(){
        $this->env = Dotenv::create($_SERVER["DOCUMENT_ROOT"]);

        try{
            $this->env->load();
        } catch (\Exception $exception) {
            errorReporter::report($this, 1, $exception->getMessage());
        }

        switch (getenv('APPLICATION_TYPE')){
            case 'API':
                $this->applicationType = self::MINIMALISM_API;
                break;
            case 'APP':
            default:
                $this->applicationType = self::MINIMALISM_APP;
                break;
        }

        $this->baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'].'/';
        $databaseNames = getenv('DATABASE');
        $this->debugKey = getenv('DEBUG');

        $this->clientId = getenv('CLIENT_ID');
        $this->clientSecret = getenv('CLIENT_SECRET');


        if ($databaseNames != false){
            $databases = explode(',', $databaseNames);
            foreach ($databases as $databaseName){
                $databaseConnection = getenv($databaseName);

                $databaseConfiguration = array();
                list($databaseConfiguration['databasename'], $databaseConfiguration['type'], $databaseConfiguration['host'], $databaseConfiguration['user'], $databaseConfiguration['password']) = explode(',', $databaseConnection);

                if (!$this->initialiseDatabase($databaseConfiguration)) errorReporter::report($this, 2);
            }
        }
    }

    /**
     * @return string
     */
    public function getErrorLog(){
        return($this->rootDirectory . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . date('Ymd', time()) . '.log');
    }

    /**
     * @return string
     */
    public function getNamespace(){
        return($this->namespace);
    }

    /**
     * @return string
     */
    public function getDebugKey(){
        $returnValue = $this->debugKey;

        if (!isset($returnValue) || !$returnValue) $returnValue='';

        return($returnValue);
    }

    /**
     * @return string
     */
    public function getBaseUrl(){
        return($this->baseUrl);
    }

    public function refreshConnections(){
        if (isset($this->cryogen) && sizeof($this->cryogen) > 0){
            /** @var cryogen $cryogen */
            foreach ($this->cryogenConnections as $cryogenConnection){
                $this->initialiseDatabase($cryogenConnection);
            }
        }
    }

    protected function initialiseDatabase($databaseConfiguration){
        try {
            $this->cryogenConnections[$databaseConfiguration['databasename']] = $databaseConfiguration;
            $connectionBuilder = connectionBuilder::bootstrap($databaseConfiguration);
            $this->cryogen[$databaseConfiguration['databasename']] = cryogenBuilder::bootstrap($connectionBuilder);

            $this->initialiseDatabaseFactories($databaseConfiguration['databasename']);
        } catch (\Exception $exception){
            return (false);
        }

        return(true);
    }

    private function initialiseDatabaseFactories($databaseName){
        if ($databaseName == 'minimalism'){
            $namespace = 'carlonicora\\minimalism\\databases\\minimalism';
            $databaseConfigFiles = __dir__ .'..'. DIRECTORY_SEPARATOR . 'databases' . DIRECTORY_SEPARATOR . $databaseName;
        } else {
            $namespace = $this->namespace . '\\databases\\' . $databaseName;
            $databaseConfigFiles = $this->appDirectory. DIRECTORY_SEPARATOR . 'databases' . DIRECTORY_SEPARATOR . $databaseName;
        }

        if (!file_exists($databaseConfigFiles)) return(true);

        try {
            foreach (new \DirectoryIterator($databaseConfigFiles) as $file) {
                if ($file->isDot()) continue;

                $fileName = $file->getBasename();
                if (strlen($fileName) >= 12 && strtolower(substr($fileName, -12, 8)) == 'dbloader') {
                    $class = substr($fileName, 0, -4);
                    $fullClassName = '\\' . $namespace . $class;
                    $fullClassName::initialise($this->cryogen[$databaseName]);
                }
            }
        } catch (\Exception $exception){
            errorReporter::report($this, 7);
        }

        return(true);
    }

    protected function refreshSessionConfigurations(){
        $_SESSION['configurations'] = $this;
    }
}

if (!function_exists('getallheaders'))  {
    function getallheaders()
    {
        if (!is_array($_SERVER)) {
            return array();
        }

        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}