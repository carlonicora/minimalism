<?php
namespace carlonicora\minimalism\databases\minimalism;

use carlonicora\cryogen\cryogen;
use carlonicora\cryogen\queryEngine;
use carlonicora\cryogen\entityList;
use carlonicora\cryogen\entity;
use carlonicora\cryogen\metaTable;
use Exception;

abstract class dbLoader{
    /**
     * @var queryEngine
     */
    protected static $engine;

    /**
     * @var string
     */
    protected static $database;

    /**
     * @var metaTable $metaTable
     */
    protected static $metaTable;

    /** @var cryogen $cryogen */
    private static $cryogen;

    /**
     * @param cryogen $cryogen
     */
    public static function initialise($cryogen){
        self::$cryogen = $cryogen;
    }

    /**
     * Initialises the dbLoader
     *
     * @param bool $reset
     * @throws Exception
     */
    protected static function init($reset=true){
        if ($reset) {
            $className = get_called_class();
            $dbName = substr($className, 0, -8);

            self::$metaTable = $dbName::$table;

            self::$engine = self::$cryogen->generateQueryEngine(self::$metaTable);
        }
    }

    /**
     * Returns a single stream matching the engine's specifications
     *
     * @return entity
     * @throws Exception
     */
    protected static function getSingle(){
        return(self::$cryogen->readSingle(self::$engine));
    }

    /**
     * Returns a list of streams matching the engine's specifications
     *
     * @return entityList
     * @throws Exception
     */
    protected static function getList(){
        return(self::$cryogen->read(self::$engine));
    }

    /**
     * Returns a list of streams matching the engine's specifications
     *
     * @return entityList
     * @throws Exception
     */
    protected static function getListComplete(){
        $returnValue = self::$cryogen->read(self::$engine);

        if (!isset($returnValue)) $returnValue = new entityList(self::$metaTable);

        return($returnValue);
    }

    /**
     * Returns the record count
     *
     * @param bool $reset
     * @return int
     * @throws Exception
     */
    public static function count($reset=true){
        self::init($reset);

        return(self::$cryogen->count(self::$engine));
    }

    public static function loadAll(){
        self::init();

        return(self::getListComplete());
    }

    /**
     * @param $id
     * @return entity
     * @throws Exception
     */
    public static function loadFromId($id){
        self::init();

        if (sizeof(self::$metaTable->getKeyFields()) != 1) throw new Exception('');

        self::$engine->setDiscriminant(self::$metaTable->getKeyFields()[0], $id);

        return(self::getSingle());
    }

    /**
     * Deletes an entity from the database
     *
     * @param entity|entityList|null $entity
     * @param bool $reset
     * @return bool
     * @throws Exception
     */
    public static function delete($entity, $reset=true){
        self::init($reset);

        if (isset($entity)){
            $returnValue = self::$cryogen->delete($entity);
        } else {
            $returnValue = self::$cryogen->delete(null, self::$engine);
        }

        return($returnValue);
    }

    /**
     * Updates an entity from the database
     *
     * @param entity|entityList $entity
     * @return bool
     * @throws Exception
     */
    public static function update(&$entity){
        self::init();

        return(self::$cryogen->update($entity));
    }

    /**
     * Truncates the table
     *
     * @return bool
     * @throws Exception
     */
    public static function truncate(){
        self::init();

        return(self::$cryogen->truncateTable(self::$metaTable));
    }
}
