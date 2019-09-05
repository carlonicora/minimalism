<?php
namespace carlonicora\minimalism\databases;

use carlonicora\minimalism\library\database\AbstractDatabaseManager;
use carlonicora\minimalism\library\exceptions\dbRecordNotFoundException;

class auth extends AbstractDatabaseManager
{
    protected $dbToUse = 'minimalism';
    protected $fields = [
        'authId'=>self::PARAM_TYPE_INTEGER,
        'userId'=>self::PARAM_TYPE_INTEGER,
        'clientId'=>self::PARAM_TYPE_INTEGER,
        'expirationDate'=>self::PARAM_TYPE_STRING,
        'publicKey'=>self::PARAM_TYPE_STRING,
        'privateKey'=>self::PARAM_TYPE_STRING];

    protected $primaryKey = [
        'authId'=>self::PARAM_TYPE_INTEGER];

    protected $autoIncrementField = 'authId';

    /**
     * @param $publicKey
     * @param $clientId
     * @return array|null
     * @throws dbRecordNotFoundException
     */
    public function loadFromPublicKeyAndClientId($publicKey, $clientId): ?array
    {
        $sql = 'SELECT * FROM auth WHERE publicKey = ? AND clientId = ?;';
        $parameters = ['si', $publicKey, $clientId];

        return $this->runReadSingle($sql, $parameters);
    }

    /**
     * @param $publicKey
     * @return array|null
     * @throws dbRecordNotFoundException
     */
    public function loadFromPublicKey($publicKey): ?array
    {
        $sql = 'SELECT * FROM auth WHERE publicKey = ?;';
        $parameters = ['s', $publicKey];

        return $this->runReadSingle($sql, $parameters);
    }

    /**
     * @return bool
     */
    public function deleteOldTokens(): bool
    {
        $sql = 'DELETE FROM auth WHERE expirationDate < ?;';
        $parameters = ['s', date('Y-m-d H:i:s')];

        return $this->runSql($sql, $parameters);
    }

    /**
     * @param $publicKey
     * @return bool
     */
    public function deleteFromPublicKey($publicKey): bool
    {
        $sql = 'DELETE from auth WHERE publicKey = ?;';
        $parameters = ['s', $publicKey];

        return $this->runSql($sql, $parameters);
    }
}