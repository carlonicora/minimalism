<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\databases\auth;
use carlonicora\minimalism\databases\clients;
use carlonicora\minimalism\library\interfaces\ConfigurationsInterface;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Dotenv\Dotenv;
use carlonicora\minimalism\helpers\errorReporter;
use Exception;
use ReflectionClass;
use ReflectionException;
use mysqli;
use DI\ContainerBuilder;

abstract class abstractConfigurations implements ConfigurationsInterface {
    public const MINIMALISM_APP = 1;
    public const MINIMALISM_API = 2;
    public const MINIMALISM_CLI = 3;

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

    /** @var bool */
    public $allowUnsafeApiCalls;

    public const DB_AUTH = auth::class;
    public const DB_CLIENTS = clients::class;

    /** @var Container */
    private $dependencies;

    abstract public function serialiseCookies(): string;
    abstract public function unserialiseCookies(string $cookies): void;

    public function __construct(){
        $child = static::class;

        $this->initialiseDirectoryStructure();

        try {
            $class_info = new ReflectionClass($child);
        } catch (ReflectionException $e) {
            $class_info = '';
        }

        $this->appDirectory = dirname($class_info->getFileName());

        $builder = new ContainerBuilder();
        try {
            $this->dependencies = $builder->build();
        } catch (Exception $e) {}
    }

    public function loadConfigurations(): void
    {
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
        if ($this->applicationType !== self::MINIMALISM_CLI) {
            $this->baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        }
        $this->debugKey = getenv('DEBUG');

        $this->allowUnsafeApiCalls = getenv('ALLOW_UNSAFE_API_CALLS') ?? false;

        $this->clientId = getenv('CLIENT_ID');
        $this->clientSecret = getenv('CLIENT_SECRET');

        $this->httpHeaderSignature = getenv('HEADER_SIGNATURE');
        if (empty($this->httpHeaderSignature)) {
            $this->httpHeaderSignature = 'Minimalism-Signature';
        }

        $dbNames = getenv('DATABASES');
        if (!empty($dbNames)) {
            $dbNames = explode(',', $dbNames);
            foreach ($dbNames ?? array() as $dbName) {
                $dbName = trim($dbName);
                $dbConnection = getenv(trim($dbName));
                $dbConf = array();
                [$dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']] = explode(',', $dbConnection);

                if (!array_key_exists($dbName, $this->databaseConnectionStrings)) {
                    $this->databaseConnectionStrings[$dbName] = $dbConf;
                }
            }
        }
    }

    /**
     * Initialises the directory structure required by minimalism
     */
    private function initialiseDirectoryStructure(): void
    {
        $backTrace = debug_backtrace();
        $callerFileName = $backTrace[count($backTrace)-1]['file'];
        $rootDir = dirname($callerFileName);

        $this->rootDirectory = $_SERVER['DOCUMENT_ROOT'];

        if ($rootDir !== $this->rootDirectory){
            $this->rootDirectory = $rootDir;
        }

        if (empty($this->rootDirectory)) {
            $this->rootDirectory = getenv('PWD');
        }

        $directoryLog = $this->rootDirectory . DIRECTORY_SEPARATOR . 'logs';

        if (!file_exists($directoryLog) && !mkdir($directoryLog) && !is_dir($directoryLog)) {
            errorReporter::returnHttpCode('Cannot create log directory');
        }
    }

    public function build(string $className){
        $response = null;

        try {
            $response = $this->dependencies->get($className);
        } catch (DependencyException $e) {
        } catch (NotFoundException $e) {
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getErrorLog(): string
    {
        return($this->rootDirectory . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . date('Ymd') . '.log');
    }

    /**
     * @return string
     */
    public function getDebugKey(): string
    {
        $returnValue = $this->debugKey;

        if (!isset($returnValue) || !$returnValue) {
            $returnValue = '';
        }

        return $returnValue;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function refreshSessionConfigurations(): void
    {
        $_SESSION['configurations'] = $this;
    }

    /**
     * @param string $databaseName
     * @return mysqli|null
     */
    public function getDatabase($databaseName): ?mysqli
    {
        $response = null;

        if (isset($this->databases) && array_key_exists($databaseName, $this->databases)){
            $response = $this->databases[$databaseName];
        }

        return $response;
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

        return $response;
    }

    /**
     * @param string $databaseName
     * @param mysqli $database
     */
    public function setDatabase($databaseName, $database): void
    {
        $this->databases[$databaseName] = $database;
    }
}