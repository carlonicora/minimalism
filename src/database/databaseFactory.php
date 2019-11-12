<?php
namespace carlonicora\minimalism\database;

use carlonicora\minimalism\interfaces\configurationsInterface;
use mysqli;

class databaseFactory {
    /** @var configurationsInterface */
    protected static $configurations;

    /**
     * @param $configurations
     */
    public static function initialise($configurations): void {
        self::$configurations = $configurations;
    }

    /**
     * @param string $dbReader
     * @return abstractDatabaseManager
     */
    public static function create($dbReader): abstractDatabaseManager {
        $response = null;

        if (!class_exists($dbReader)){
            return null;
        }

        /** @var abstractDatabaseManager $response */
        $response = new $dbReader();

        $databaseName = $response->getDbToUse();
        $connection = self::$configurations->getDatabase($databaseName);

        $dbConf = self::$configurations->getDatabaseConnectionString($databaseName);

        if (!isset($connection) && isset($dbConf)){
            $connection = new mysqli($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']);
        }

        if (isset($connection) && !isset($connection->thread_id)) {
            $connection->connect($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']);
        }

        if (!isset($connection) || $connection->connect_errno) {
            return null;
        }

        $connection->set_charset('utf8mb4');

        $response->setConnection($connection);

        return $response;
    }
}