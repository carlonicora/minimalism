<?php
namespace carlonicora\minimalism\interfaces;

use carlonicora\minimalism\helpers\logger;
use mysqli;

interface configurationsInterface{
    /**
     * @param string $databaseName
     * @return mysqli|null
     */
    public function getDatabase($databaseName): ?mysqli;

    /**
     *
     */
    public function cleanNonPersistentVariables(): void;

    /**
     * @param string $databaseName
     * @return null|array
     */
    public function getDatabaseConnectionString($databaseName): ?array;

    /**
     * @param string $databaseName
     * @param mysqli $database
     */
    public function setDatabase($databaseName, $database);

    /**
     * @return logger
     */
    public function getLogger(): logger;
}