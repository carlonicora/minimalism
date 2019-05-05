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

    /** @var Dotenv $env */
    protected $env;

    /** @var string $baseUrl */
    private $baseUrl;

    /** @var string $debugKey */
    protected $debugKey;

    /** @var array */
    protected $cryogen = array();

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
        $databaseNames = getenv('DATABASE');
        $this->debugKey = getenv('DEBUG');

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

    protected function initialiseDatabase($databaseConfiguration){
        try {
            $connectionBuilder = connectionBuilder::bootstrap($databaseConfiguration);
            $this->cryogen[$databaseConfiguration['name']] = cryogenBuilder::bootstrap($connectionBuilder);

            $this->initialiseDatabaseFactories($databaseConfiguration['name']);
        } catch (\Exception $exception){
            return (false);
        }

        return(true);
    }

    private function initialiseDatabaseFactories($databaseName){
        $databaseConfigFiles = $this->appDirectory. DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . $databaseName;

        if (!file_exists($databaseConfigFiles)) return(true);

        try {
            foreach (new \DirectoryIterator($databaseConfigFiles) as $file) {
                if ($file->isDot()) continue;

                $fileName = $file->getBasename();
                if (strlen($fileName) >= 12 && strtolower(substr($fileName, -12, 8)) == 'dbloader') {
                    $class = substr($fileName, 0, -4);
                    $fullClassName = '\\' . $this->namespace . '\\databases\\' . $class;
                    $fullClassName::initialise($this->cryogen);
                }
            }
        } catch (\Exception $exception){
            errorReporter::report($this, 7);
        }

        return(true);
    }
}