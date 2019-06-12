<?php
namespace carlonicora\minimalism\abstracts;

use mysqli;

abstract class dbReader
{
    /** @var mysqli */
    private $connection;

    /** @var string */
    protected $dbToUse;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getDbToUse(): string
    {
        return $this->dbToUse;
    }

    /**
     * @param mysqli $connection
     */
    public function setConnection(mysqli $connection)
    {
        $this->connection = $connection;
    }
}