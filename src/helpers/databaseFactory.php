<?php
namespace carlonicora\minimalism\helpers;

use carlonicora\minimalism\abstracts\configurations;
use carlonicora\minimalism\abstracts\databaseManager;
use mysqli;

class databaseFactory {
    /** @var configurations */
    protected static $configurations;

    public static function initialise($configurations){
        self::$configurations = $configurations;
    }

    /**
     * @param string $dbReader
     * @return databaseManager
     */
    public static function create($dbReader){
        $response = null;

        if (!class_exists($dbReader)){
            return(null);
        }

        /** @var databaseManager $response */
        $response = new $dbReader();

        $databaseName = $response->getDbToUse();
        $connection = self::$configurations->getDatabase($databaseName);

        $saveConnection = !isset($connection);

        $dbConf = self::$configurations->getDatabaseConnectionString($databaseName);

        if (!isset($connection) && isset($dbConf)){
            $connection = new mysqli($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']);
        }

        if (isset($connection) && !isset($connection->thread_id)) {
            $connection->connect($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']);
        }

        if (!isset($connection) || $connection->connect_errno) return (null);

        $connection->set_charset("utf8");

        $response->setConnection($connection);

        if ($saveConnection)
        {
            self::$configurations->setDatabase($dbConf['dbName'], $connection);
        }

        return($response);
    }
}