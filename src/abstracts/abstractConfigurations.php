<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\database\databaseFactory;
use carlonicora\minimalism\databases\security\securityDb;
use carlonicora\minimalism\helpers\logger;
use carlonicora\minimalism\interfaces\configurationsInterface;
use carlonicora\minimalism\interfaces\securityClientInterface;
use carlonicora\minimalism\interfaces\securitySessionInterface;
use Dotenv\Dotenv;
use carlonicora\minimalism\helpers\errorReporter;
use Exception;
use ReflectionClass;
use ReflectionException;
use mysqli;

abstract class abstractConfigurations implements configurationsInterface {
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
    private $databases = [];

    /** @var array */
    private $databaseConnectionStrings = [];

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
    public $httpHeaderSignature;

    /** @var bool */
    public $allowUnsafeApiCalls;

    /** @var string */
    private $logDirectory;

    /** @var logger */
    public $logger;

    /** @var securityClientInterface */
    protected $securityClient;

    /** @var securitySessionInterface */
    protected $securitySession;

    abstract public function serialiseCookies(): string;
    abstract public function unserialiseCookies(string $cookies): void;

    /**
     * abstractConfigurations constructor.
     */
    public function __construct(){
        $child = static::class;

        $this->initialiseDirectoryStructure();

        try {
            $class_info = new ReflectionClass($child);
        } catch (ReflectionException $e) {
            $class_info = '';
        }

        $this->appDirectory = dirname($class_info->getFileName());
    }

    /**
     *
     */
    public function loadConfigurations(): void {
        $this->env = Dotenv::createImmutable($this->rootDirectory);

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

        $logEvents = getenv('MINIMALISM_LOG_EVENTS');
        if (!empty($logEvents) && $logEvents === 'true'){
            $this->logger->setLogEvents(true);
        }
        $logQueries = getenv('MINIMALISM_LOG_QUERIES');
        if (!empty($logQueries) && $logQueries === 'true'){
            $this->logger->setLogQueries(true);
        }

        $this->httpHeaderSignature = getenv('HEADER_SIGNATURE');
        if (empty($this->httpHeaderSignature)) {
            $this->httpHeaderSignature = 'Minimalism-Signature';
        }

        $dbNames = getenv('DATABASES');
        if (!empty($dbNames)) {
            $dbNames = explode(',', $dbNames);
            foreach ($dbNames ?? [] as $dbName) {
                $dbName = trim($dbName);
                $dbConnection = getenv(trim($dbName));
                $dbConf = [];
                [$dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']] = explode(',', $dbConnection);

                if (!array_key_exists($dbName, $this->databaseConnectionStrings)) {
                    $this->databaseConnectionStrings[$dbName] = $dbConf;
                }
            }
        }

        databaseFactory::initialise($this);

        $this->securityClient = securityDb::clients();
        $this->securitySession = securityDb::auth();
    }

    /**
     * Initialises the directory structure required by minimalism
     */
    private function initialiseDirectoryStructure(): void {
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

        $this->logDirectory = $this->rootDirectory . DIRECTORY_SEPARATOR . 'logs';

        if (!file_exists($this->logDirectory) && !mkdir($this->logDirectory) && !is_dir($this->logDirectory)) {
            errorReporter::returnHttpCode('Cannot create log directory');
        }

        $this->logDirectory .= DIRECTORY_SEPARATOR . 'minimalism';

        if (!file_exists($this->logDirectory) && !mkdir($this->logDirectory) && !is_dir($this->logDirectory)) {
            errorReporter::returnHttpCode('Cannot create log directory');
        }

        $this->logger = new logger($this->logDirectory . DIRECTORY_SEPARATOR);
    }

    /**
     * @return logger
     */
    public function getLogger(): logger {
        return $this->logger;
    }

    /**
     * @return string
     */
    public function getErrorLog(): string {
        return($this->logDirectory . DIRECTORY_SEPARATOR . date('Ymd') . '.log');
    }

    /**
     * @return string
     */
    public function getDebugKey(): string {
        $returnValue = $this->debugKey;

        if (!isset($returnValue) || !$returnValue) {
            $returnValue = '';
        }

        return $returnValue;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string {
        return $this->baseUrl;
    }

    /**
     *
     */
    protected function refreshSessionConfigurations(): void {
        $_SESSION['configurations'] = $this;
    }

    /**
     * @param string $databaseName
     * @return mysqli|null
     */
    public function getDatabase($databaseName): ?mysqli {
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
    public function getDatabaseConnectionString($databaseName): array {
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
    public function setDatabase($databaseName, $database): void {
        $this->databases[$databaseName] = $database;
    }

    /**
     *
     */
    public function cleanNonPersistentVariables(): void{
        $this->databases = [];
    }

    /**
     * @return securityClientInterface
     */
    public function getSecurityClient(): securityClientInterface {
        return $this->securityClient;
    }

    /**
     * @return securitySessionInterface
     */
    public function getSecuritySession(): securitySessionInterface {
        return $this->securitySession;
    }
}