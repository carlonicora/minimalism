<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\library\interfaces\ConfigurationsInterface;
use Dotenv\Dotenv;
use carlonicora\minimalism\helpers\errorReporter;
use Exception;
use ReflectionClass;
use ReflectionException;
use mysqli;

abstract class AbstractConfigurations implements ConfigurationsInterface {
    const MINIMALISM_APP = 1;
    const MINIMALISM_API = 2;
    const MINIMALISM_CLI = 3;

    /** @var string $namespace */
    private $namespace;

    /** @var string $rootDirectory */
    protected $rootDirectory;

    /** @var string */
    public $appDirectory;

    /** @var Dotenv $env */
    protected $env;

    /** @var string $baseUrl */
    private $baseUrl;

    /** @var string */
    protected $debugKey;

    /** @var array */
    private $databases = array();

    /** @var array */
    private $databaseConnectionStrings = array();

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

    /** @var string */
    public $userId;

    /** @var string */
    public $httpHeaderSignature;

    const DB_AUTH = 'carlonicora\\minimalism\\databases\\auth';
    const DB_CLIENTS = 'carlonicora\\minimalism\\databases\\clients';

    public function __construct($namespace){
        $child = get_called_class();

        $this->initialiseDirectoryStructure();

        try {
            $class_info = new ReflectionClass($child);
        } catch (ReflectionException $e) {
            $class_info = '';
        }

        $this->appDirectory = dirname($class_info->getFileName());

        $this->namespace = $namespace;
    }

    public function loadConfigurations(){
        $this->env = Dotenv::create($this->rootDirectory);

        try{
            $this->env->load();
        } catch (Exception $exception) {
            errorReporter::report($this, 1, $exception->getMessage());
        }

        switch (getenv('APPLICATION_TYPE')){
            case 'API':
                $this->applicationType = self::MINIMALISM_API;
                break;
            case 'CLI':
                $this->applicationType = self::MINIMALISM_CLI;
                break;
            case 'APP':
            default:
                $this->applicationType = self::MINIMALISM_APP;
                break;
        }
        if ($this->applicationType != self::MINIMALISM_CLI) $this->baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'].'/';
        $this->debugKey = getenv('DEBUG');

        $this->clientId = getenv('CLIENT_ID');
        $this->clientSecret = getenv('CLIENT_SECRET');

        $this->httpHeaderSignature = getenv('HEADER_SIGNATURE');
        if (empty($this->httpHeaderSignature)) $this->httpHeaderSignature = 'Minimalism-Signature';

        $dbNames = explode(',', getenv('DATABASES'));
        foreach (isset($dbNames) ? $dbNames : array() as $dbName){
            $dbConnection = getenv(trim($dbName));
            $dbConf = array();
            list($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']) = explode(',', $dbConnection);

            if (!array_key_exists($dbConf['dbName'], $this->databaseConnectionStrings)){
                $this->databaseConnectionStrings[$dbConf['dbName']] = $dbConf;
            }
        }
    }

    /**
     * Initialises the directory structure required by minimalism
     */
    private function initialiseDirectoryStructure(){
        $backTrace = debug_backtrace();
        $callerFileName = $backTrace[sizeof($backTrace)-1]['file'];
        $rootDir = dirname($callerFileName);

        $this->rootDirectory = $_SERVER["DOCUMENT_ROOT"];

        if ($rootDir != $this->rootDirectory){
            $this->rootDirectory = $rootDir;
        }

        if (empty($this->rootDirectory)) $this->rootDirectory = getenv('PWD');

        $directoryLog = $this->rootDirectory . DIRECTORY_SEPARATOR . 'logs';

        if (!file_exists($directoryLog) && !mkdir($directoryLog)) errorReporter::returnHttpCode('Cannot create log directory');
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

    protected function refreshSessionConfigurations(){
        $_SESSION['configurations'] = $this;
    }

    /**
     * @param string $databaseName
     * @return mysqli|null
     */
    public function getDatabase($databaseName)
    {
        $response = null;

        if (isset($this->databases) && array_key_exists($databaseName, $this->databases)){
            $response = $this->databases[$databaseName];
        }

        return ($response);
    }

    /**
     * @param string $databaseName
     * @return array
     */
    public function getDatabaseConnectionString($databaseName): array
    {
        $response = null;

        if (isset($this->databaseConnectionStrings) && array_key_exists($databaseName, $this->databaseConnectionStrings)){
            $response = $this->databaseConnectionStrings[$databaseName];
        }

        return ($response);
    }

    /**
     * @param string $databaseName
     * @param mysqli $database
     */
    public function setDatabase($databaseName, $database)
    {
        $this->databases[$databaseName] = $database;
    }
}