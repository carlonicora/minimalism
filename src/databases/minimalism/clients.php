<?php
namespace carlonicora\minimalism\databases\minimalism;

use carlonicora\cryogen\entity;
use carlonicora\cryogen\metaTable;
use carlonicora\cryogen\metaField;
use stdClass;

class clients extends entity{
    const LOADER = 'minimalism\clientsDbLoader';

    protected $_initialValues;

    public $id;
    public $name;
    public $description;
    public $URL;
    public $callbackURL;
    public $clientId;
    public $clientSecret;

    public static $table;
    public static $field_id;
    public static $field_name;
    public static $field_description;
    public static $field_URL;
    public static $field_callbackURL;
    public static $field_clientId;
    public static $field_clientSecret;

    /**
     * Initialises the action entity
     *
     * @param entity $entity
     * @param null $id
     * @param null $name
     * @param null $description
     * @param null $URL
     * @param null $callbackURL
     * @param null $clientId
     * @param null $clientSecret
     */
    public function __construct(entity $entity=null, $id=null, $name=null, $description=null, $URL=null, $callbackURL=null, $clientId=null, $clientSecret=null){
        $this->metaTable = self::$table;
        $this->_initialValues = [];

        parent::__construct($entity);

        if (isset($id)) $this->id = $id;
        if (isset($name)) $this->name = $name;
        if (isset($description)) $this->description = $description;
        if (isset($URL)) $this->URL = $URL;
        if (isset($callbackURL)) $this->callbackURL = $callbackURL;
        if (isset($clientId)) $this->clientId = $clientId;
        if (isset($clientSecret)) $this->clientSecret = $clientSecret;
    }

    public function returnJSON(){
        $json = new stdClass();

        $returnValue = json_encode($json);

        return($returnValue);
    }
}
clients::$table = new metaTable("clients", "\\carlonicora\\minimalism\\databases\\minimalism\\clients");
clients::$field_id = new metaField(0, "id", "int", 11, TRUE, TRUE);
clients::$field_name = new metaField(1, "name", "varchar", 255);
clients::$field_description = new metaField(2, "description", "varchar", 255);
clients::$field_URL = new metaField(3, "URL", "varchar", 255);
clients::$field_callbackURL = new metaField(4, "callbackURL", "varchar", 255);
clients::$field_clientId = new metaField(5, "clientId", "varchar", 32);
clients::$field_clientSecret = new metaField(6, "clientSecret", "varchar", 32);

clients::$table->fields[] = clients::$field_id;
clients::$table->fields[] = clients::$field_name;
clients::$table->fields[] = clients::$field_description;
clients::$table->fields[] = clients::$field_URL;
clients::$table->fields[] = clients::$field_callbackURL;
clients::$table->fields[] = clients::$field_clientId;
clients::$table->fields[] = clients::$field_clientSecret;