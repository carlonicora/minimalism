<?php
namespace carlonicora\minimalism\services\database\factories;

use carlonicora\minimalism\core\exceptions\configurationException;
use carlonicora\minimalism\core\services\abstracts\abstractServiceFactory;
use carlonicora\minimalism\services\database\configurations\databaseConfigurations;
use carlonicora\minimalism\services\database\database;
use carlonicora\minimalism\core\services\factories\servicesFactory;

class serviceFactory  extends abstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param servicesFactory $services
     * @throws configurationException
     */
    public function __construct(servicesFactory $services) {
        $this->configData = new databaseConfigurations();

        parent::__construct($services);
    }

    /**
     * @param servicesFactory $services
     * @return database
     */
    public function create(servicesFactory $services): database {
        return new database($this->configData, $services);
    }
}