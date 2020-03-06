<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\databases\dataObject;
use carlonicora\minimalism\exceptions\dbRecordNotFoundException;
use carlonicora\minimalism\exceptions\dbUpdateException;
use carlonicora\minimalism\helpers\logger;
use mysqli;
use Exception;

abstract class abstractDatabaseManager {
    public const RECORD_STATUS_NEW = 1;
    public const RECORD_STATUS_UNCHANGED = 2;
    public const RECORD_STATUS_UPDATED = 3;
    public const RECORD_STATUS_DELETED = 4;

    /**
     * New
     */
    public const INTEGER=0b1;
    public const DOUBLE=0b10;
    public const STRING=0b100;
    public const BLOB=0b1000;
    public const PRIMARY_KEY=0b10000;
    public const AUTO_INCREMENT=0b100000;

    public const INSERT_IGNORE = ' IGNORE';

    /** @var mysqli */
    private $connection;

    /** @var string */
    protected $dbToUse;

    /** @var string */
    protected $autoIncrementField;

    /** @var array */
    protected $fields;

    /** @var array */
    protected $primaryKey;

    /** @var string */
    protected $tableName;

    /** @var logger */
    private $logger;

    /** @var string */
    protected $insertIgnore = '';

    /**
     * abstractDatabaseManager constructor.
     */
    public function __construct() {
        $fullName = get_class($this);
        $fullNameParts = explode('\\', $fullName);

        if (!isset($this->tableName)){
            $this->tableName = end($fullNameParts);
        }

        if (!isset($this->dbToUse) && isset($fullNameParts[count($fullNameParts)-1]) && $fullNameParts[count($fullNameParts)-2] === 'tables'){
            $this->dbToUse = $fullNameParts[count($fullNameParts)-3];
        }

        if (!isset($this->primaryKey)){
            foreach ($this->fields as $fieldName=>$fieldFlags){
                if (($fieldFlags & self::PRIMARY_KEY) > 0){
                    /** @noinspection NotOptimalIfConditionsInspection */
                    if (!isset($this->primaryKey)){
                        $this->primaryKey = [];
                    }
                    $this->primaryKey[$fieldName]=$fieldFlags;
                }
            }
        }

        if (!isset($this->autoIncrementField)){
            foreach ($this->fields as $fieldName=>$fieldFlags){
                if (($fieldFlags & self::AUTO_INCREMENT) > 0){
                    $this->autoIncrementField = $fieldName;
                    break;
                }
            }
        }
    }

    /**
     * @param logger $logger
     */
    public function setLogger(logger $logger): void {
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getDbToUse(): string {
        return $this->dbToUse;
    }

    /**
     * @param mysqli $connection
     */
    public function setConnection(mysqli $connection): void {
        $this->connection = $connection;
    }

    /**
     * @param array $records
     * @throws dbUpdateException
     */
    public function delete($records): void {
        $this->update($records, true);
    }

    /**
     * @param string $sql
     * @param array $parameters
     * @return bool
     */
    public function runSql($sql, $parameters=null): bool {
        $response = true;

        try{
            $this->connection->autocommit(false);

            $this->logger->addQuery($sql, $parameters);

            $statement = $this->connection->prepare($sql);

            if ($statement) {
                if (!empty($parameters)) {
                    call_user_func_array(array($statement, 'bind_param'), $this->refValues($parameters));
                }
                if (!$statement->execute()) {
                    $this->logger->addError('MySQL error on execute:' . PHP_EOL . $sql . PHP_EOL . $this->connection->error . PHP_EOL . PHP_EOL);
                    $this->connection->rollback();
                    $response = false;
                }
            } else {
                $this->logger->addError('MySQL error on prepare:' . PHP_EOL . $sql . PHP_EOL . $this->connection->error . PHP_EOL . PHP_EOL);
            }

            $this->connection->autocommit(true);
        } catch (Exception $e){
            $this->logger->addError('MySQL generic error:' . PHP_EOL . $sql . PHP_EOL . $this->connection->error . PHP_EOL . PHP_EOL);
            $this->connection->rollback();
            $response = false;
        }

        return $response;
    }

    /**
     * @param array $records
     * @param bool $delete
     * @throws dbUpdateException
     */
    public function update(&$records, $delete=false): void {
        $isSingle = false;

        if (isset($records) && count($records) > 0){
            if (!array_key_exists(0, $records)){
                $isSingle = true;
                $records = [$records];
            }

            $onlyInsertOrUpdate = true;
            $oneSql = $this->generateInsertOnDuplicateUpdateStart();
            /**
             * @var $recordKey
             * @var dataObject $record
             */
            foreach ($records as $recordKey=>$record) {
                if ($delete){
                    $status = self::RECORD_STATUS_DELETED;
                } else {
                    $status = $record->status();
                }

                if ($status !== self::RECORD_STATUS_UNCHANGED) {
                    $oneSql .= $this->generateInsertOnDuplicateUpdateRecord($record);

                    $records[$recordKey]['sql'] = array();
                    $records[$recordKey]['sql']['status'] = $status;

                    $parameters = [];
                    $parametersToUse = null;

                    switch ($status) {
                        case self::RECORD_STATUS_NEW:
                            $records[$recordKey]->sql = $this->generateInsertStatement();
                            $parametersToUse = $this->generateInsertParameters();
                            break;
                        case self::RECORD_STATUS_UPDATED:
                            $records[$recordKey]->sql = $this->generateUpdateStatement();
                            $parametersToUse = $this->generateUpdateParameters();
                            break;
                        case self::RECORD_STATUS_DELETED:
                            $onlyInsertOrUpdate = false;
                            $records[$recordKey]->sql = $this->generateDeleteStatement();
                            $parametersToUse = $this->generateDeleteParameters();
                            break;

                    }

                    foreach ($parametersToUse as $parameter){
                        if (count($parameters) === 0){
                            $parameters[] = $parameter;
                        } else if (array_key_exists($parameter, $record)){
                            $parameters[] = $record[$parameter];
                        } else {
                            $parameters[] = null;
                        }
                    }
                    $records[$recordKey]->parameters = $parameters;
                }
            }

            $oneSql = substr($oneSql, 0, -1);
            $oneSql .= $this->generateInsertOnDuplicateUpdateEnd();

            if ($onlyInsertOrUpdate && !$isSingle && $this->canUseInsertOnDuplicate()){
                if (!$this->runSql($oneSql)){
                    throw new dbUpdateException('Update failed');
                }
            } else if (!$this->runUpdate($records)){
                throw new dbUpdateException('Update failed');
            }
        }

        if ($isSingle){
            $records = $records[0];
        }
    }

    /**
     * @param string $sql
     * @param array $parameters
     * @return dataObject
     */
    protected function runRead($sql, $parameters=null): dataObject {
        $this->logger->addQuery($sql, $parameters);

        $statement = $this->connection->prepare($sql);
        if (isset($parameters)) {
            call_user_func_array(array($statement, 'bind_param'), $this->refValues($parameters));
        }

        $statement->execute();

        $results = $statement->get_result();

        $response = new dataObject();

        if (!empty($results) && $results->num_rows > 0){
            while ($record = $results->fetch_assoc()){
                $dataObjectRecord = new dataObject();
                $dataObjectRecord->addValues($record);
                $dataObjectRecord->addOriginalValues();

                $response[] = $dataObjectRecord;
            }
        }

        $statement->close();

        return $response;
    }

    /**
     * @param array $objects
     * @return bool
     * @throws dbUpdateException
     */
    protected function runUpdate(&$objects): bool {
        $response = true;

        $this->connection->autocommit(false);

        foreach ($objects as $objectKey=>$object){
            if (array_key_exists('sql', $object)) {
                $statement = $this->connection->prepare($object['sql']['statement']);

                if ($statement) {

                    $this->logger->addQuery($object['sql']['statement'], $object['sql']['parameters']);

                    $parameters = $object['sql']['parameters'];
                    call_user_func_array(array($statement, 'bind_param'), $this->refValues($parameters));
                    if (!$statement->execute()) {
                        $this->logger->addError('MySQL error on execute:' . PHP_EOL . $object['sql']['statement'] . PHP_EOL . $this->connection->error . PHP_EOL . PHP_EOL);
                        $this->connection->rollback();
                        throw new dbUpdateException('Statement Execution failed: ' .
                            $object['sql']['statement'] .
                            ' with parameters ' . json_encode($object['sql']['parameters'], JSON_THROW_ON_ERROR, 512));
                    }
                } else {
                    $this->logger->addError('MySQL error on prepare:' . PHP_EOL . $object['sql']['statement'] . PHP_EOL . $this->connection->error . PHP_EOL . PHP_EOL);
                    $this->connection->rollback();
                    throw new dbUpdateException('Statement creation failed: ' .
                        $objects[$objectKey]['sql']['statement']);
                }

                if (isset($this->autoIncrementField) && $object['sql']['status'] === self::RECORD_STATUS_NEW) {
                    $objects[$objectKey][$this->autoIncrementField] = $this->connection->insert_id;
                }

                unset($objects[$objectKey]['sql']);

                $objects[$objectKey]->addOriginalValues();
            }
        }

        $this->connection->autocommit(true);

        return $response;
    }

    /**
     * @param string $sql
     * @param string $parameters
     * @return dataObject
     * @throws dbRecordNotFoundException
     */
    protected function runReadSingle($sql, $parameters=null): dataObject {
        $response = $this->runRead($sql, $parameters);

        if (isset($response)) {
            if (count($response) === 0){
                throw new dbRecordNotFoundException('Record not found');
            }

            if (count($response) === 1){
                $response = $response[0];
            } else {
                throw new dbRecordNotFoundException('Multiple records found');
            }
        } else {
            throw new dbRecordNotFoundException('Record not found!');
        }


        return $response;
    }

    /**
     * @param $arr
     * @return array
     */
    private function refValues($arr): array {
        $refs = [];

        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }

        return $refs;
    }

    /**
     * @return string
     */
    private function generateSelectStatement(): string {
        $response = 'SELECT * FROM ' . $this->tableName . ' WHERE ';

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $response .= $fieldName . '=? AND ';
        }

        $response = substr($response, 0, -5);

        $response .= ';';

        return $response;
    }

    /**
     * @param int|string $fieldType
     * @return string
     */
    private function convertFieldType($fieldType): string {
        if (is_int($fieldType)){
            if (($fieldType & self::INTEGER) > 0){
                $fieldType = 'i';
            } else if (($fieldType & self::DOUBLE) > 0){
                $fieldType = 'd';
            } else if (($fieldType & self::STRING) > 0){
                $fieldType = 's';
            } else {
                $fieldType = 'b';
            }
        }

        return ($fieldType);
    }

    /**
     * @return array
     */
    private function generateSelectParameters(): array {
        $response = array();

        $response[] = '';

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $fieldType = $this->convertFieldType($fieldType);
            $response[0] .= $fieldType;
            $response[] = $fieldName;
        }

        return $response;
    }

    /**
     * @return bool
     */
    private function canUseInsertOnDuplicate(): bool {
        if (isset($this->primaryKey)) {
            foreach ($this->fields as $fieldName => $fieldType) {
                if (!array_key_exists($fieldName, $this->primaryKey)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return string
     */
    private function generateInsertOnDuplicateUpdateStart(): string {
        $response = 'INSERT INTO ' . $this->tableName . ' (';

        foreach ($this->fields as $fieldName=>$fieldType){
            $response .= $fieldName . ',';
        }

        $response = substr($response, 0, -1);

        $response .= ') VALUES ';

        return $response;
    }

    /**
     * @param array $record
     * @return string
     */
    private function generateInsertOnDuplicateUpdateRecord(array $record): string {
        $response = '(';

        foreach ($this->fields as $fieldName=>$fieldType){
            $fieldType = $this->convertFieldType($fieldType);
            $fieldValue = 'NULL';
            if (array_key_exists($fieldName, $record) && $record[$fieldName] !== NULL) {
                $fieldValue = $record[$fieldName];
            }

            if ($fieldType === 'i' && (is_bool($fieldValue))){
                $fieldValue = $fieldValue ? 1 : 0;
            }

            if ($fieldValue !== 'NULL' && ($fieldType === 's' || $fieldType === 'b')){
                $response .= '\'' . $fieldValue . '\',';
            } else {
                $response .= $fieldValue . ',';
            }
        }
        $response = substr($response, 0, -1);
        $response .= '),';

        return $response;
    }

    /**
     * @return string
     */
    private function generateInsertOnDuplicateUpdateEnd(): string {
        $response = ' ON DUPLICATE KEY UPDATE ';

        foreach ($this->fields as $fieldName=>$fieldType){
            if (!array_key_exists($fieldName, $this->primaryKey)) {
                $response .= $fieldName . '=VALUES(' . $fieldName . '),';
            }
        }
        $response = substr($response, 0, -1);

        $response .= ';';

        return ($response);
    }


    /**
     * @return string
     */
    private function generateInsertStatement(): string {
        $response = 'INSERT' . $this->insertIgnore . ' INTO ' . $this->tableName . ' (';

        $parameterList = '';
        foreach ($this->fields as $fieldName=>$fieldType){
            $response .= $fieldName . ', ';
            $parameterList .= '?, ';
        }

        $response = substr($response, 0, -2);
        $parameterList = substr($parameterList, 0, -2);

        $response .= ') VALUES (' . $parameterList . ');';

        return $response;
    }

    /**
     * @return array
     */
    private function generateInsertParameters(): array {
        $response = array();

        $response[] = '';

        foreach ($this->fields as $fieldName=>$fieldType){
            $fieldType = $this->convertFieldType($fieldType);
            $response[0] .= $fieldType;
            $response[] = $fieldName;
        }

        return $response;
    }

    /**
     * @return string
     */
    private function generateDeleteStatement(): string {
        $response = 'DELETE FROM ' . $this->tableName . ' WHERE ';

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $response .= $fieldName . '=? AND ';
        }

        $response = substr($response, 0, -5);

        $response .= ';';

        return $response;
    }

    /**
     * @return array
     */
    private function generateDeleteParameters(): array {
        $response = array();

        $response[] = '';

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $fieldType = $this->convertFieldType($fieldType);
            $response[0] .= $fieldType;
            $response[] = $fieldName;
        }

        return $response;
    }

    /**
     * @return string
     */
    private function generateUpdateStatement(): string {
        $response = 'UPDATE ' . $this->tableName . ' SET ';

        foreach ($this->fields as $fieldName=>$fieldType){
            if (!array_key_exists($fieldName, $this->primaryKey)){
                $response .= $fieldName . '=?, ';
            }
        }

        $response = substr($response, 0, -2);

        $response .= ' WHERE ';

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $response .= $fieldName . '=? AND ';
        }

        $response = substr($response, 0, -5);

        $response .= ';';

        return $response;
    }

    /**
     * @return array
     */
    private function generateUpdateParameters(): array {
        $response = array();

        $response[] = '';

        foreach ($this->fields as $fieldName=>$fieldType){
            if (!array_key_exists($fieldName, $this->primaryKey)) {
                $fieldType = $this->convertFieldType($fieldType);
                $response[0] .= $fieldType;
                $response[] = $fieldName;
            }
        }

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $fieldType = $this->convertFieldType($fieldType);
            $response[0] .= $fieldType;
            $response[] = $fieldName;
        }

        return $response;
    }

    /**
     * @param $id
     * @return dataObject
     * @throws dbRecordNotFoundException
     */
    public function loadFromId($id): dataObject {
        $sql = $this->generateSelectStatement();
        $parameters = $this->generateSelectParameters();

        $parameters[1] = $id;

        return $this->runReadSingle($sql, $parameters);
    }

    /**
     * @return dataObject|null
     */
    public function loadAll(): ?dataObject {
        $sql = 'SELECT * FROM ' . $this->tableName . ';';

        return $this->runRead($sql);
    }

    /**
     * @return int
     */
    public function count(): int {
        $sql = 'SELECT count(*) as counter FROM ' . $this->tableName . ';';

        try {
            $responseArray = $this->runReadSingle($sql);
            $response = $responseArray['counter'];
        } catch (dbRecordNotFoundException $e) {
            $response = 0;
        }

        return $response;
    }
}