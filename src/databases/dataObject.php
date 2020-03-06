<?php
namespace carlonicora\minimalism\databases;

use ArrayObject;
use carlonicora\minimalism\abstracts\abstractDatabaseManager;

class dataObject extends ArrayObject {
    /** @var array */
    public $originalValues;

    /** @var string */
    public $sql;

    /** @var array */
    public $parameters;

    /**
     * @return int
     */
    public function status(): int {
        if ($this->originalValue !== null) {
            $response = abstractDatabaseManager::RECORD_STATUS_UNCHANGED;
            foreach ($this->originalValues ?? [] as $fieldName => $originalValue) {
                if ($originalValue !== $this[$fieldName]) {
                    $response = abstractDatabaseManager::RECORD_STATUS_UPDATED;
                    break;
                }
            }
        } else {
            $response = abstractDatabaseManager::RECORD_STATUS_NEW;
        }

        return $response;
    }

    /**
     * @param array $values
     */
    public function addValues(array $values) : void {
        foreach ($values as $recordKey=>$recordValue) {
            $this[$recordKey] = $recordValue;
        }
    }

    /**
     */
    public function addOriginalValues(): void {
        $this->originalValues = [];
        $iteratable = $this->getIterator();
        foreach($iteratable as $fieldName=>$fieldValue){
            $originalValues[$fieldName] = $fieldValue;
        }
    }
}