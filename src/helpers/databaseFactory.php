<?php
namespace carlonicora\minimalism\helpers;

use carlonicora\minimalism\abstracts\configurations;
use carlonicora\minimalism\abstracts\dbReader;
use mysqli;
use ReflectionClass;
use Exception;

class databaseFactory {
    /** @var configurations */
    protected static $configurations;

    public static function initialise($configurations){
        self::$configurations = $configurations;
    }

    /**
     * @param string $dbReaderName
     * @param string|null $dbReaderNamespace
     * @return dbReader
     */
    public static function create($dbReaderName, $dbReaderNamespace=null){
        if (!isset($dbReaderNamespace)){
            $dbReaderNamespace = self::$configurations->getNamespace();
        }

        $dbReaderClass = $dbReaderNamespace . $dbReaderName;

        if (!class_exists($dbReaderClass)){
            return(null);
        }

        /** @var dbReader $response */
        try {
            $response = new ReflectionClass($dbReaderClass);
        } catch (Exception $e){
            return(null);
        }

        $databaseName = $response->getDbToUse();
        $connection = self::$configurations->getDatabase($databaseName);

        if (!isset($response)){
            $dbConf = self::$configurations->getDatabaseConnectionString($databaseName);

            if (isset($dbConf)){
                $connection = new mysqli($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']);
            }
        }

        if ($connection->connect_errno) return(null);

        $response->setConnection($connection);

        return($response);
    }












    /**
     * @param string $loaderName
     * @return databaseLoader
     */
    public static function getLoader($loaderName){
        $response = null;

        $fullName = self::$configurations->getNamespace() . '\\databases\\' . $loaderName;

        if (class_exists($fullName)){
            $response = new $fullName(self::$configurations);
        } else {
            $fullName = 'carlonicora\\minimalism\\databases\\' . $loaderName;
            if (class_exists($fullName)){
                $response = new $fullName(self::$configurations);
            } else {
                $response = new databaseLoader(self::$configurations);
            }
        }

        return($response);
    }
}