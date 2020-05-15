<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Services\Factories;

use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Services\Logger\Factories\ServiceFactory;
use CarloNicora\Minimalism\Services\Logger\Logger;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use JsonException;

class ServicesFactoryTest extends AbstractTestCase
{
    public function testServiceNotFound() : void
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->services->service('Something Not Existing');
    }

    public function testLoadDependency() : void
    {
        $this->setProperty($this->services, 'services', []);

        $this->services->loadService(ServiceFactory::class);

        $this->assertNotNull($this->services->service(Logger::class));
    }

    /**
     * @throws JsonException
     */
    public function testSerialiseCookied() : void
    {
        $this->services->loadService(ServiceFactory::class);

        $this->assertNotNull($this->services->serialiseCookies());
    }

    public function testFailUnserialiseCookies() : void
    {
        $_COOKIE['cookieDough'] = 'not a valid json';

        $this->services->unserialiseCookies('cookieDough');

        $this->assertNotNull($_COOKIE['cookieDough']);
    }

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    public function testDestroyStatics() : void
    {
        /** @noinspection UnnecessaryAssertionInspection */
        $this->assertNull($this->services->destroyStatics());
    }
}