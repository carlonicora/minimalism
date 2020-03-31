<?php
namespace carlonicora\minimalism\services\database\factories;

use carlonicora\minimalism\services\database\configuration\databaseConfigurations;
use carlonicora\minimalism\services\database\database;
use carlonicora\minimalism\services\factories\servicesFactory;
use carlonicora\minimalism\services\interfaces\serviceFactoryInterface;

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