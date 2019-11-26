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

        $response->setLogger(self::$configurations->getLogger());

        $databaseName = $response->getDbToUse();
        $connection = self::$configurations->getDatabase($databaseName);

        if (!isset($connection)){
            $dbConf = self::$configurations->getDatabaseConnectionString($databaseName);

            if (empty($dbConf)){
                return null;
            }

            $connection = new mysqli($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']);

            $connection->connect($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']);

            if ($connection->connect_errno) {
                return null;
            }

            $connection->set_charset('utf8mb4');

            self::$configurations->setDatabase($databaseName, $connection);
        }

        $response->setConnection($connection);

        return $response;
    }
}