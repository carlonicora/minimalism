<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractApiModel;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;

class AbstractApiModelTest extends AbstractTestCase
{
    public function testDefaultConstruction() : void
    {
        $mock = $this->getMockForAbstractClass(AbstractApiModel::class, [$this->services]);

        $this->assertEquals('GET', $this->getProperty($mock, 'verb'));
    }

    public function testConstruction() : void
    {
        $mock = $this->getMockForAbstractClass(AbstractApiModel::class, [$this->services]);
        $mock->setVerb('PUT');

        $this->assertEquals('PUT', $this->getProperty($mock, 'verb'));
    }

    public function testGetParameters() : void
    {
        $parameters = [
            'GET' => [
                'one' => ['name' => 'one']
            ]
        ];

        $mock = $this->getMockForAbstractClass(AbstractApiModel::class, [$this->services]);

        $this->setProperty($mock, 'parameters', $parameters);

        $this->assertEquals($parameters['GET'], $this->invokeMethod($mock, 'getParameters'));
    }

    public function testEmptyGetParameters() : void
    {
        $parameters = [
            'PUT' => [
                'one' => ['name' => 'one']
            ]
        ];

        $mock = $this->getMockForAbstractClass(AbstractApiModel::class, [$this->services]);

        $this->setProperty($mock, 'parameters', $parameters);

        $this->assertEquals([], $this->invokeMethod($mock, 'getParameters'));
    }

    public function testRequiresAuth() : void
    {
        $mock = $this->getMockForAbstractClass(AbstractApiModel::class, [$this->services]);
        $this->assertFalse($mock->requiresAuth('GET'));
    }
}