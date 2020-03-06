<?php
namespace carlonicora\minimalism\factories;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\abstracts\abstractDatabaseManager;
use carlonicora\minimalism\exceptions\dbConnectionException;
use carlonicora\minimalism\helpers\errorReporter;
use mysqli;

class databaseFactory {
    /** @var abstractConfigurations */
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
     * @throws dbConnectionException
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
                throw new dbConnectionException('Missing connection details');
            }

            $connection = new mysqli($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']);

            $connection->connect($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']);

            if ($connection->connect_errno) {
                errorReporter::report(self::$configurations, 21, $connection->connect_error);
                throw new dbConnectionException($connection->connect_error);
            }

            $connection->set_charset('utf8mb4');

            self::$configurations->setDatabase($databaseName, $connection);
        }

        $response->setConnection($connection);

        return $response;
    }
}