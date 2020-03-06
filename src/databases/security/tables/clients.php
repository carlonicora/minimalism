<?php
namespace carlonicora\minimalism\databases\security\tables;

use carlonicora\minimalism\abstracts\abstractDatabaseManager;
use carlonicora\minimalism\databases\dataObject;
use carlonicora\minimalism\exceptions\dbRecordNotFoundException;
use carlonicora\minimalism\interfaces\securityClientInterface;
use Exception;
use RuntimeException;

class clients extends abstractDatabaseManager implements securityClientInterface{
    protected $fields = [
        'id'=>self::INTEGER+self::PRIMARY_KEY+self::AUTO_INCREMENT,
        'name'=>self::STRING,
        'description'=>self::STRING,
        'url'=>self::STRING,
        'callbackURL'=>self::STRING,
        'clientId'=>self::STRING,
        'clientSecret'=>self::STRING];

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
     * @return dataObject
     * @throws dbRecordNotFoundException
     */
    public function loadFromClientId($clientId): dataObject {
        $sql = 'SELECT * FROM clients WHERE clientId = ?;';
        $parameters = ['s', $clientId];

        return $this->runReadSingle($sql, $parameters);
    }
}