<?php
namespace carlonicora\minimalism\abstracts;

use mysqli;
use Exception;

abstract class databaseManager
{
    use databaseManagerCommonTraits;
    use databaseManagerGeneratorTrait;

    const PARAM_TYPE_INTEGER = 'i';
    const PARAM_TYPE_DOUBLE = 'd';
    const PARAM_TYPE_STRING = 's';
    const PARAM_TYPE_BLOB = 'b';

    const RECORD_STATUS_NEW = 1;
    const RECORD_STATUS_UNCHANGED = 2;
    const RECORD_STATUS_UPDATED = 3;
    const RECORD_STATUS_DELETED = 4;

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

    public function __construct()
    {
        if (!isset($this->tableName)){
            $fullName = get_class($this);
            $fullNameParts = explode('\\', $fullName);
            $this->tableName = end($fullNameParts);
        }
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

    /**
     * @param array $records
     * @return bool
     */
    public function delete($records){
        return($this->update($records, true));
    }

    /**
     * @param string $sql
     * @param array $parameters
     * @return bool
     */
    public function runSql($sql, $parameters){
        $response = true;

        try{
            $this->connection->autocommit(false);

            $statement = $this->connection->prepare($sql);

            if ($statement) {
                call_user_func_array(array($statement, 'bind_param'), $this->refValues($parameters));
                if (!$statement->execute()) {
                    $this->connection->rollback();
                    $response = false;
                }
            }

            $this->connection->autocommit(true);
        } catch (Exception $e){
            $this->connection->rollback();
            $response = false;
        }

        return($response);
    }

    /**
     * @param array $records
     * @param bool $delete
     * @return bool
     */
    public function update(&$records, $delete=false){
        $response = array();

        $isSingle = false;

        if (isset($records) && sizeof($records) > 0){
            if (!array_key_exists(0, $records)){
                $isSingle = true;
                $records= [$records];
            }

            foreach ($records as &$record) {
                if ($delete){
                    $status = self::RECORD_STATUS_DELETED;
                } else {
                    $status = $this->status($record);
                }

                if ($status != self::RECORD_STATUS_UNCHANGED) {
                    $record['sql'] = array();
                    $record['sql']['status'] = $status;

                    if (isset($sql)){
                        $record['sql']['statement'] = $sql;
                        if (isset($parameters)){
                            $record['sql']['parameters'] = $parameters;
                        }
                    } else {
                        $parameters = [];
                        $parametersToUse = null;

                        switch ($status) {
                            case self::RECORD_STATUS_NEW:
                                $record['sql']['statement'] = $this->generateInsertStatement();
                                $parametersToUse = $this->generateInsertParameters();
                                break;
                            case self::RECORD_STATUS_UPDATED:
                                $record['sql']['statement'] = $this->generateUpdateStatement();
                                $parametersToUse = $this->generateUpdateParameters();
                                break;
                            case self::RECORD_STATUS_DELETED:
                                $record['sql']['statement'] = $this->generateDeleteStatement();
                                $parametersToUse = $this->generateDeleteParameters();
                                break;

                        }

                        foreach ($parametersToUse as $parameter){
                            if (sizeof($parameters) == 0){
                                $parameters[] = $parameter;
                            } else {
                                if (array_key_exists($parameter, $record)){
                                    $parameters[] = $record[$parameter];
                                } else {
                                    $parameters[] = null;
                                }
                            }
                        }
                        $record['sql']['parameters'] = $parameters;
                    }
                }
            }

            $response = $this->runUpdate($records);
        }

        if ($isSingle){
            $records = $records[0];
        }

        return($response);
    }

    /**
     * @param string $sql
     * @param array $parameters
     * @return array|null
     */
    protected function runRead($sql, $parameters=null){
        $response = null;

        $statement = $this->connection->prepare($sql);
        if (isset($parameters)) call_user_func_array(array($statement, 'bind_param'), $this->refValues($parameters));

        $statement->execute();

        $results = $statement->get_result();

        if (isset($results)){
            $response = array();

            while ($record = $results->fetch_assoc()){
                $this->addOriginalValues($record);

                $response[] = $record;
            }
        }

        $statement->close();

        return($response);
    }

    /**
     * @param array $objects
     * @return bool
     */
    protected function runUpdate(&$objects){
        $response = true;
        try{
            $this->connection->autocommit(false);

            foreach ($objects as &$object){
                if (array_key_exists('sql', $object)) {
                    $statement = $this->connection->prepare($object['sql']['statement']);

                    if ($statement) {
                        $parameters = $object['sql']['parameters'];
                        call_user_func_array(array($statement, 'bind_param'), $this->refValues($parameters));
                        if (!$statement->execute()) {
                            $this->connection->rollback();
                            $response = false;
                            break;
                        }
                    } else {
                        $this->connection->rollback();
                        $response = false;
                        break;
                    }

                    if ($object['sql']['status'] == self::RECORD_STATUS_NEW && isset($this->autoIncrementField)) {
                        $object[$this->autoIncrementField] = $this->connection->insert_id;
                    }

                    unset($object['sql']);

                    $this->addOriginalValues($object);
                }
            }

            $this->connection->autocommit(true);
        } catch (Exception $e){
            $this->connection->rollback();
            $response = false;
        }

        return($response);
    }

    /**
     * @param string $sql
     * @param string $parameters
     * @return array|null
     */
    protected function runReadSingle($sql, $parameters=null){
        $response = $this->runRead($sql, $parameters);

        if (isset($response) && sizeof($response) == 0){
            $response = null;
        } else if (isset($response) && sizeof($response) == 1){
            $response = $response[0];
        }

        return($response);
    }

    /**
     * @param $record
     * @return int
     */
    protected function status($record){
        if (array_key_exists('originalValues', $record)){
            $response = self::RECORD_STATUS_UNCHANGED;
            foreach ($record['originalValues'] as $fieldName=>$originalValue){
                if ($originalValue != $record[$fieldName]){
                    $response = self::RECORD_STATUS_UPDATED;
                    break;
                }
            }
        } else {
            $response = self::RECORD_STATUS_NEW;
        }

        return($response);
    }

    /**
     * @param array $record
     */
    private function addOriginalValues(&$record){
        $originalValues = array();
        foreach($record as $fieldName=>$fieldValue){
            $originalValues[$fieldName] = $fieldValue;
        }
        $record['originalValues'] = $originalValues;
    }

    /**
     * @param $arr
     * @return array
     */
    private function refValues($arr) {
        $refs = [];

        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }

        return $refs;
    }
}