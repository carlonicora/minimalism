<?php
namespace carlonicora\minimalism\databases;

use carlonicora\minimalism\library\database\abstractDatabaseManager;
use carlonicora\minimalism\library\exceptions\dbRecordNotFoundException;

class clients extends abstractDatabaseManager
{
    protected $dbToUse = 'minimalism';
    protected $fields = [
        'id'=>self::PARAM_TYPE_INTEGER,
        'name'=>self::PARAM_TYPE_STRING,
        'description'=>self::PARAM_TYPE_STRING,
        'url'=>self::PARAM_TYPE_STRING,
        'callbackURL'=>self::PARAM_TYPE_STRING,
        'clientId'=>self::PARAM_TYPE_STRING,
        'clientSecret'=>self::PARAM_TYPE_STRING];

    protected $primaryKey = [
        'id'=>self::PARAM_TYPE_INTEGER];

    protected $autoIncrementField = 'id';

    /**
     * @param $clientId
     * @return array|null
     * @throws dbRecordNotFoundException
     */
    public function loadFromClientId($clientId): ?array
    {
        $sql = 'SELECT * FROM clients WHERE clientId = ?;';
        $parameters = ['s', $clientId];

        return $this->runReadSingle($sql, $parameters);
    }
}