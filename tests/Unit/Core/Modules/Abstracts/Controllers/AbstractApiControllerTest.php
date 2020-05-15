<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractApiController;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractApiControllerTest extends AbstractTestCase
{
    /** @var MockObject|AbstractApiController */
    protected MockObject $controller;

    public function setUp(): void
    {
        parent::setUp();
        unset($_SERVER['HTTP_X_HTTP_METHOD']);

    }

    /**
     * @throws Exception
     */
    public function testInitialiseModel() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->controller = $this->getMockForAbstractClass(AbstractApiController::class, [$this->services]);
        $this->controller->initialiseParameters([
            'requiredEncryptedParameter' => '1'
        ]);

        $this->setProperty($this->services->paths(), 'root', './tests/Unit/Mocks/MockComposer');
        $this->controller->initialiseModel('ApiModel');

        $this->assertEquals(1, 1);
    }

    /**
     * @throws Exception
     */
    public function testInitialisePOSTModel() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->controller = $this->getMockForAbstractClass(AbstractApiController::class, [$this->services]);
        $this->controller->initialiseParameters([
            'requiredEncryptedParameter' => '1'
        ]);

        $this->setProperty($this->services->paths(), 'root', './tests/Unit/Mocks/MockComposer');
        $this->controller->initialiseModel('ApiModel');

        $model = $this->getProperty($this->controller, 'model');
        $verb = $this->getProperty($model, 'verb');

        $this->assertEquals('POST', $verb);
    }

    /**
     * @throws Exception
     */
    public function testInitialiseDELETEModel() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_HTTP_METHOD'] = 'DELETE';
        $this->controller = $this->getMockForAbstractClass(AbstractApiController::class, [$this->services]);
        $this->controller->initialiseParameters([
            'requiredEncryptedParameter' => '1'
        ]);

        $this->setProperty($this->services->paths(), 'root', './tests/Unit/Mocks/MockComposer');
        $this->controller->initialiseModel('ApiModel');

        $model = $this->getProperty($this->controller, 'model');
        $verb = $this->getProperty($model, 'verb');

        $this->assertEquals('DELETE', $verb);
    }

    /**
     * @throws Exception
     */
    public function testInitialisePUTModel() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_HTTP_METHOD'] = 'PUT';
        $this->controller = $this->getMockForAbstractClass(AbstractApiController::class, [$this->services]);
        $this->controller->initialiseParameters([
            'requiredEncryptedParameter' => '1'
        ]);

        $this->setProperty($this->services->paths(), 'root', './tests/Unit/Mocks/MockComposer');
        $this->controller->initialiseModel('ApiModel');

        $model = $this->getProperty($this->controller, 'model');
        $verb = $this->getProperty($model, 'verb');

        $this->assertEquals('PUT', $verb);
    }
}