<?php
namespace carlonicora\minimalism\abstracts;

use CarloNicora\cryogen\connectionBuilder;
use CarloNicora\cryogen\cryogenBuilder;
use Dotenv\Dotenv;
use carlonicora\minimalism\helpers\errorReporter;

abstract class configurations{
    /** @var string $namespace */
    private $namespace;

    /** @var string $rootDirectory */
    private $rootDirectory;

    /** @var string */
    public $appDirectory;

    /** @var string $databaseConnection */
    public $databaseConnection;

    /** @var Dotenv $env */
    protected $env;

    /** @var string $baseUrl */
    private $baseUrl;

    /** @var string $debugKey */
    protected $debugKey;

    /**
     * configurations constructor.
     * @param $namespace
     */
    public function __construct($namespace){
        $child = get_called_class();
        $class_info = new \ReflectionClass($child);
        $this->appDirectory = dirname($class_info->getFileName());

        $this->namespace = $namespace;
        $this->rootDirectory = $_SERVER["DOCUMENT_ROOT"];
    }

    public function loadConfigurations(){
        $this->env = Dotenv::create($_SERVER["DOCUMENT_ROOT"]);

        try{
            $this->env->load();
        } catch (\Exception $exception) {
            errorReporter::report($this, 1, $exception->getMessage());
        }

        $this->baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'].'/';
        $this->databaseConnection = getenv('DATABASE');
        $this->debugKey = getenv('DEBUG');

        /**
         * @TODO reinstantiate
         */
        /*
        if ($this->databaseConnection != false){
            if (!$this->initialiseDatabase()) errorReporter::report($this, 2);
            $this->initialiseDatabaseFactories();
        }
        */
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

    /**
     * @TODO check database intialisation
     */
    private function initialiseDatabase(){
        if (!$this->databaseConnection) return (true);

        $databaseConfiguration = array();
        list($databaseConfiguration['databasename'], $databaseConfiguration['type'], $databaseConfiguration['host'], $databaseConfiguration['user'], $databaseConfiguration['password']) = explode(',', $this->databaseConnection);

        try {
            $connectionBuilder = connectionBuilder::bootstrap($databaseConfiguration);
            $this->cryogen = cryogenBuilder::bootstrap($connectionBuilder);
        } catch (\Exception $exception){
            return (false);
        }

        return(true);
    }

    /**
     * @TODO check database factories initialisation
     */
    /*
    private function initialiseDatabaseFactories(){
        if (!file_exists($this->directoryDatabase)) return(true);

        try {
            foreach (new DirectoryIterator($this->directoryDatabase) as $file) {
                if ($file->isDot()) continue;

                $fileName = $file->getBasename();
                if (strlen($fileName) >= 12 && strtolower(substr($fileName, -12, 8)) == 'dbloader') {
                    $class = substr($fileName, 0, -4);
                    $fullClassName = '\\' . $this->appNamespace . '\\databases\\' . $class;
                    $fullClassName::initialise($this->cryogen);
                }
            }
        } catch (Exception $exception){
            return($this->errorManager->reportError(errorManager::ERROR_CRYOGEN_INITIALISATION, $exception->getMessage()));
        }

        return(true);
    }
    */
}