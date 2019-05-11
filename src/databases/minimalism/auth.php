<?php
namespace carlonicora\minimalism\databases\minimalism;

use carlonicora\cryogen\entity;
use carlonicora\cryogen\metaTable;
use carlonicora\cryogen\metaField;
use stdClass;

class auth extends entity{
    protected $_initialValues;

    public $id;
    public $userId;
    public $clientId;
    public $expirationDate;
    public $publicKey;
    public $privateKey;

    public static $table;
    public static $field_id;
    public static $field_userId;
    public static $field_clientId;
    public static $field_expirationDate;
    public static $field_publicKey;
    public static $field_privateKey;

    /**
     * Initialises the action entity
     *
     * @param entity $entity
     * @param null $id
     * @param null $userId
     * @param null $clientId
     * @param null $expirationDate
     * @param null $publicKey
     * @param null $privateKey
     */
    public function __construct(entity $entity=null, $id=null, $userId=null, $clientId=null, $expirationDate=null, $publicKey=null, $privateKey=null){
        $this->metaTable = self::$table;
        $this->_initialValues = [];

        parent::__construct($entity);

        if (isset($id)) $this->id = $id;
        if (isset($userId)) $this->userId = $userId;
        if (isset($clientId)) $this->clientId = $clientId;
        if (isset($expirationDate)) $this->expirationDate = $expirationDate;
        if (isset($publicKey)) $this->publicKey = $publicKey;
        if (isset($privateKey)) $this->privateKey = $privateKey;
    }

    public function returnPublicObject(){
        $returnValue = new stdClass();
        $returnValue->privateKey = $this->privateKey;

        return($returnValue);
    }
}
auth::$table = new metaTable("auth", "\\carlonicora\\minimalism\\databases\\minimalism\\auth");
auth::$field_id = new metaField(0, "id", "int", 11, TRUE, TRUE);
auth::$field_userId = new metaField(1, "userId", "int", 11);
auth::$field_clientId = new metaField(2, "clientId", "int", 11);
auth::$field_expirationDate = new metaField(3, "expirationDate", "timestamp");
auth::$field_publicKey = new metaField(4, "publicKey", "varchar", 32);
auth::$field_privateKey = new metaField(5, "privateKey", "varchar", 32);

auth::$table->fields[] = auth::$field_id;
auth::$table->fields[] = auth::$field_userId;
auth::$table->fields[] = auth::$field_clientId;
auth::$table->fields[] = auth::$field_expirationDate;
auth::$table->fields[] = auth::$field_publicKey;
auth::$table->fields[] = auth::$field_privateKey;