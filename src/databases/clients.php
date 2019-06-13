<?php
namespace carlonicora\minimalism\databases;

use carlonicora\minimalism\library\database\AbstractDatabaseManager;

class clients extends AbstractDatabaseManager
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

    public function loadFromClientId($clientId){
        $sql = 'SELECT * FROM clients WHERE clientId = ?;';
        $parameters = ['s', $clientId];

        $response = $this->runReadSingle($sql, $parameters);

        return($response);
    }
}