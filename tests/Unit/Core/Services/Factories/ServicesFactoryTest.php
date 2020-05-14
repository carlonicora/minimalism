<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Services\Factories;

use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Factories\ServiceFactory;
use CarloNicora\Minimalism\Services\Logger\Logger;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use JsonException;

class ServicesFactoryTest extends AbstractTestCase
{
    public function testServiceNotFound() : void
    {
        $services = new ServicesFactory();

        $this->expectException(ServiceNotFoundException::class);

        $services->service('Something Not Existing');
    }

    public function testLoadDependency() : void
    {
        $services = new ServicesFactory();
        $this->setProperty($services, 'services', []);

        $services->loadService(ServiceFactory::class);

        $this->assertNotNull($services->service(Logger::class));
    }

    public function testSerialiseCookied() : void
    {
        $services = new ServicesFactory();
        $services->loadService(ServiceFactory::class);

        $this->assertNotNull($services->serialiseCookies());
    }

    public function testFailUnserialiseCookies() : void
    {
        $services = new ServicesFactory();
        $services->loadService(ServiceFactory::class);

        $_COOKIE['cookieDough'] = 'not a valid json';

        $services->unserialiseCookies('cookieDough');

        $this->assertNotNull($_COOKIE['cookieDough']);
    }

    public function testDestroyStatics() : void
    {
        $services = new ServicesFactory();
        $this->assertNull($services->destroyStatics());
    }
}