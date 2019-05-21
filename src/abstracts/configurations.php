<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\cryogen\connectionBuilder;
use carlonicora\cryogen\cryogen;
use carlonicora\cryogen\cryogenBuilder;
use DirectoryIterator;
use Dotenv\Dotenv;
use carlonicora\minimalism\helpers\errorReporter;
use Exception;
use ReflectionClass;
use ReflectionException;

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

    /** @var string */
    public $userId;

    public function __construct($namespace){
        $child = get_called_class();

        try {
            $class_info = new ReflectionClass($child);
        } catch (ReflectionException $e) {
            $class_info = '';
        }

        $this->appDirectory = dirname($class_info->getFileName());

        $this->namespace = $namespace;
        $this->rootDirectory = $_SERVER["DOCUMENT_ROOT"];
    }

    public function loadConfigurations(){
        $this->env = Dotenv::create($_SERVER["DOCUMENT_ROOT"]);

        try{
            $this->env->load();
        } catch (Exception $exception) {
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
        $databaseNames = getenv('DATABASES');
        $this->debugKey = getenv('DEBUG');

        $this->clientId = getenv('CLIENT_ID');
        $this->clientSecret = getenv('CLIENT_SECRET');


        if ($databaseNames != false){
            $databases = explode(',', $databaseNames);
            foreach ($databases as $databaseName){
                $databaseConnection = getenv($databaseName);

                $databaseConfiguration = array();
                $databaseConfiguration['databasename'] = $databaseName;
                list($databaseConfiguration['type'], $databaseConfiguration['host'], $databaseConfiguration['user'], $databaseConfiguration['password']) = explode(',', $databaseConnection);

                if (!$this->initialiseDatabase($databaseConfiguration)) errorReporter::report($this, 2);
            }

            $databaseFactoryFile = '\\' . $this->namespace . '\\databases\\databaseFactory';
            $databaseFactoryFile::initialise($this);
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
            
        } catch (Exception $exception){
            return (false);
        }

        return(true);
    }

    protected function refreshSessionConfigurations(){
        $_SESSION['configurations'] = $this;
    }
}