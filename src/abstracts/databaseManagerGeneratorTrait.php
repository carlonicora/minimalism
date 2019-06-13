<?php
namespace carlonicora\minimalism\abstracts;

trait databaseManagerGeneratorTrait
{
    /**
     * @return string
     */
    private function generateSelectStatement(){
        $response = 'SELECT * FROM ' . $this->tableName . ' WHERE ';

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $response .= $fieldName . '=? AND ';
        }

        $response = substr($response, 0, strlen($response) - 5);

        $response .= ';';

        return($response);
    }

    /**
     * @return array
     */
    private function generateSelectParameters(){
        $response = array();

        $response[] = '';

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $response[0] .= $fieldType;
            $response[] = $fieldName;
        }

        return($response);
    }

    /**
     * @return string
     */
    private function generateInsertStatement(){
        $response = 'INSERT INTO ' . $this->tableName . ' (';

        $parameterList = '';
        foreach ($this->fields as $fieldName=>$fieldType){
            $response .= $fieldName . ', ';
            $parameterList .= '?, ';
        }

        $response = substr($response, 0, strlen($response) - 2);
        $parameterList = substr($parameterList, 0, strlen($parameterList) - 2);

        $response .= ') VALUES (' . $parameterList . ');';

        return($response);
    }

    /**
     * @return array
     */
    private function generateInsertParameters(){
        $response = array();

        $response[] = '';

        foreach ($this->fields as $fieldName=>$fieldType){
            $response[0] .= $fieldType;
            $response[] = $fieldName;
        }

        return($response);
    }

    /**
     * @return string
     */
    private function generateDeleteStatement(){
        $response = 'DELETE FROM ' . $this->tableName . ' WHERE ';

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $response .= $fieldName . '=? AND ';
        }

        $response = substr($response, 0, strlen($response) - 5);

        $response .= ';';

        return($response);
    }

    /**
     * @return array
     */
    private function generateDeleteParameters(){
        $response = array();

        $response[] = '';

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $response[0] .= $fieldType;
            $response[] = $fieldName;
        }

        return($response);
    }

    /**
     * @return string
     */
    private function generateUpdateStatement(){
        $response = 'UPDATE ' . $this->tableName . ' SET ';

        foreach ($this->fields as $fieldName=>$fieldType){
            if (!array_key_exists($fieldName, $this->primaryKey)){
                $response .= $fieldName . '=?, ';
            }
        }

        $response = substr($response, 0, strlen($response) - 2);

        $response .= ' WHERE ';

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $response .= $fieldName . '=? AND ';
        }

        $response = substr($response, 0, strlen($response) - 5);

        $response .= ';';

        return($response);
    }

    /**
     * @return array
     */
    private function generateUpdateParameters(){
        $response = array();

        $response[] = '';

        foreach ($this->fields as $fieldName=>$fieldType){
            if (!array_key_exists($fieldName, $this->primaryKey)) {
                $response[0] .= $fieldType;
                $response[] = $fieldName;
            }
        }

        foreach ($this->primaryKey as $fieldName=>$fieldType){
            $response[0] .= $fieldType;
            $response[] = $fieldName;
        }

        return($response);
    }
}