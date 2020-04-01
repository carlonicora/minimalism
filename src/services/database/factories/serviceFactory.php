<?php
namespace carlonicora\minimalism\services\database\factories;

use carlonicora\minimalism\services\database\configurations\databaseConfigurations;
use carlonicora\minimalism\services\database\database;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceFactoryInterface;

class serviceFactory implements serviceFactoryInterface {
    /** @var databaseConfigurations  */
    private databaseConfigurations $configData;

    /**
     * serviceFactory constructor.
     */
    public function __construct() {
        $this->configData = new databaseConfigurations();
    }

    /**
     * @param servicesFactory $services
     * @return database
     */
    public function create(servicesFactory $services): database {
        return new database($this->configData);
    }


}