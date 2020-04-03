<?php
namespace carlonicora\minimalism\services\database;

use carlonicora\minimalism\core\exceptions\dbConnectionException;
use carlonicora\minimalism\core\services\abstracts\abstractService;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceConfigurationsInterface;
use carlonicora\minimalism\services\database\abstracts\abstractDatabaseManager;
use carlonicora\minimalism\services\database\configurations\databaseConfigurations;
use mysqli;

class database extends abstractService {
    /** @var databaseConfigurations  */
    private databaseConfigurations $configData;

    /**
     * abstractApiCaller constructor.
     * @param serviceConfigurationsInterface $configData
     * @param servicesFactory $services
     */
    public function __construct(serviceConfigurationsInterface $configData, servicesFactory $services) {
        parent::__construct($configData, $services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->configData = $configData;
    }

    /**
     * @param string $dbReader
     * @return abstractDatabaseManager
     * @throws dbConnectionException
     */
    public function create(string $dbReader): abstractDatabaseManager {
        if (array_key_exists($dbReader, $this->configData->tableManagers)) {
            return $this->configData->tableManagers[$dbReader];
        }

        if (!class_exists($dbReader)) {
            return null;
        }

        /** @var abstractDatabaseManager $response */
        $response = new $dbReader();

        $databaseName = $response->getDbToUse();
        $connection = $this->configData->getDatabase($databaseName);

        if (!isset($connection)) {
            $dbConf = $this->configData->getDatabaseConnectionString($databaseName);

            if (empty($dbConf)) {
                throw new dbConnectionException('Missing connection details');
            }

            $connection = new mysqli($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']);

            $connection->connect($dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbName'], $dbConf['port']);

            if ($connection->connect_errno) {
                throw new dbConnectionException($connection->connect_error);
            }

            $connection->set_charset('utf8mb4');

            $this->configData->setDatabase($databaseName, $connection);
        }

        $response->setConnection($connection);

        $this->configData->tableManagers[$dbReader] = $response;

        return $response;
    }

    /**
     *
     */
    public function cleanNonPersistentVariables(): void {
        $this->configData->resetDatabases();
    }
}