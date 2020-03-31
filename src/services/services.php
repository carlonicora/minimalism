<?php
namespace carlonicora\minimalism\services;

use carlonicora\minimalism\exceptions\configurationException;
use carlonicora\minimalism\services\encrypter\encrypter;
use carlonicora\minimalism\services\interfaces\serviceFactoryInterface;
use carlonicora\minimalism\services\mailer\interfaces\mailerServiceInterface;

class services {
    /** @var mailerServiceInterface  */
    public mailerServiceInterface $mailer;

    /** @var encrypter */
    public encrypter $encrypter;

    /**
     * services constructor.
     * @throws configurationException
     */
    public function __construct() {
        $this->generateService('mailer');
    }

    /**
     * @param string $serviceName
     * @throws configurationException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    private function generateService(string $serviceName) : void {
        $factoryName = 'carlonicora\\minimalism\\services\\' . $serviceName . '\\factories\\serviceFactory';

        if (class_exists($factoryName)){
            /** @var serviceFactoryInterface $service */
            $service = new $factoryName();
            $this->$serviceName = $service->create();
        }
    }
}