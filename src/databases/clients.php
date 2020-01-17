<?php
namespace carlonicora\minimalism\databases;

use carlonicora\minimalism\database\abstractDatabaseManager;
use carlonicora\minimalism\exceptions\dbRecordNotFoundException;
use carlonicora\minimalism\interfaces\securityClientInterface;
use Exception;
use RuntimeException;

class clients extends abstractDatabaseManager implements securityClientInterface{
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
     * @param string $clientId
     * @return string
     * @throws Exception
     */
    public function getSecret(string $clientId): string {
        try {
            $client = $this->loadFromClientId($clientId);
        } catch (dbRecordNotFoundException $e) {
            throw new RuntimeException('Record not found', 1);
        }

        return $client['clientSecret'];
    }


    /**
     * @param $clientId
     * @return array|null
     * @throws dbRecordNotFoundException
     */
    public function loadFromClientId($clientId): ?array {
        $sql = 'SELECT * FROM clients WHERE clientId = ?;';
        $parameters = ['s', $clientId];

        return $this->runReadSingle($sql, $parameters);
    }
}