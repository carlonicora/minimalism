<?php
namespace carlonicora\minimalism\helpers;

use carlonicora\cryogen\cryogen;
use carlonicora\cryogen\entity;
use carlonicora\cryogen\entityList;
use carlonicora\cryogen\metaTable;
use carlonicora\cryogen\queryEngine;
use carlonicora\minimalism\abstracts\configurations;

class databaseLoader {
    /** @var cryogen */
    protected $cryogen;

    /** @var queryEngine */
    protected $engine;

    /** @var metaTable */
    protected $metaTable;

    /** @var configurations */
    protected $configurations;

    /** @var string */
    private $databaseName;

    /** @var string */
    private $tableName;

    /**
     * databaseLoader constructor.
     * @param configurations $configurations
     */
    public function __construct($configurations){
        $this->configurations = $configurations;

        $className = get_called_class();
        $namespaceList = explode('\\', $className);

        $this->databaseName = $namespaceList[sizeof($namespaceList)-2];
        $this->tableName =  substr($className, 0, -8);

        $this->cryogen = $this->configurations->cryogen[$this->databaseName];

        $this->init();
    }

    /**
     * Initialises the dbLoader
     *
     * @param bool $reset
     */
    public function init($reset=true){
        if ($reset){
            $this->metaTable = $this->tableName::$table;
            $this->engine = $this->cryogen->generateQueryEngine($this->metaTable);
        }
    }

    /**
     * Returns a single stream matching the engine's specifications
     *
     * @return entity
     */
    protected function getSingle(){
        $response = $this->cryogen->readSingle($this->engine);

        $this->init();

        return($response);
    }

    /**
     * Returns a list of streams matching the engine's specifications
     *
     * @return entityList
     */
    protected function getList(){
        $response = $this->cryogen->read(($this->engine));

        $this->init();

        return($response);
    }

    /**
     * Returns a list of streams matching the engine's specifications
     *
     * @return entityList
     */
    protected function getListComplete(){
        $response = $this->cryogen->read($this->engine);

        if (!isset($response)) $response = new entityList($this->metaTable);

        $this->init();

        return($response);
    }

    /**
     * Returns the record count
     *
     * @param bool $reset
     * @return int
     */
    public function count($reset = true){
        $this->init($reset);

        $response = $this->cryogen->count($this->engine);

        $this->init();

        return($response);
    }


    public function loadAll(){
        $this->init();

        $response = $this->getListComplete();

        $this->init();

        return($response);
    }

    /**
     * @param $id
     * @return entity
     */
    public function loadFromId($id){
        $this->init();

        if (sizeof($this->metaTable->getKeyFields()) != 1) return(null);

        $this->engine->setDiscriminant($this->metaTable->getKeyFields()[0], $id);

        $response = $this->getSingle();

        $this->init();

        return($response);
    }

    /**
     * Deletes an entity from the database
     *
     * @param entity|entityList|null $entity
     * @param bool $reset
     * @return bool
     */
    public function delete($entity, $reset=true){
        $this->init($reset);
        if (isset($entity)){
            $response = $this->cryogen->delete($entity);
        } else {
            $response = $this->cryogen->delete(null, $this->engine);
        }

        $this->init();

        return($response);
    }

    /**
     * Updates an entity from the database
     *
     * @param entity|entityList $entity
     * @return bool
     */
    public function update(&$entity){
        $this->init();

        $response = $this->cryogen->update($entity);

        $this->init();

        return($response);
    }

    /**
     * Truncates the table
     *
     * @return bool
     */
    public function truncate(){
        $this->init();

        $response = $this->cryogen->truncateTable($this->metaTable);

        $this->init();

        return($response);
    }
}