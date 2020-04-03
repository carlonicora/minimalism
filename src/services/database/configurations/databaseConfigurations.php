<?php
namespace carlonicora\minimalism\services\database\configurations;

use carlonicora\minimalism\core\services\abstracts\abstractServiceConfigurations;
use mysqli;

class databaseConfigurations extends abstractServiceConfigurations {
    /** @var array */
    private array $databases = [];

    /** @var array */
    public array $databaseConnectionStrings = [];

    /** @var array */
    public array $tableManagers = [];

    /**
     * databaseConfigurations constructor.
     */
    public function __construct() {
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
    }

    /**
     * @param string $databaseName
     * @return mysqli|null
     */
    public function getDatabase($databaseName): ?mysqli {
        $response = null;

        if ($this->databases !== null && array_key_exists($databaseName, $this->databases)){
            $response = $this->databases[$databaseName];
        }

        return $response;
    }

    /**
     * @param string $databaseName
     * @return null|array
     */
    public function getDatabaseConnectionString($databaseName): ?array {
        $response = null;

        if ($this->databaseConnectionStrings !== null && array_key_exists($databaseName, $this->databaseConnectionStrings)){
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
    public function resetDatabases() : void {
        $this->databases = [];
        $this->tableManagers = [];
    }
}