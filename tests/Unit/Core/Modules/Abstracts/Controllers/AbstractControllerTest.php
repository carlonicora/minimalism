<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractController;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractControllerTest extends AbstractTestCase
{
    /** @var MockObject|AbstractController  */
    protected MockObject $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->controller = $this->getMockForAbstractClass(AbstractController::class, [$this->services]);
        unset($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    /**
     * @throws Exception
     */
    public function testInitialiseParameters() : void
    {
        $array = ['one'=>'one'];
        $this->controller = $this->controller->initialiseParameters($array);

        $this->assertEquals($array, $this->getProperty($this->controller, 'passedParameters'));
    }

    /**
     * @throws Exception
     */
    public function testInitialiseModelFailsNoComposer() : void
    {
        $this->setProperty($this->services->paths(), 'root', '/');

        $this->expectExceptionCode(500);
        $this->controller->initialiseModel();
    }

    /**
     * @throws Exception
     */
    public function testInitialiseModelFailsWrongComposer() : void
    {
        $this->setProperty($this->services->paths(), 'root', '/opt/project/tests/Unit/Mocks/WrongComposer');

        $this->expectExceptionCode(500);
        $this->controller->initialiseModel();
    }

    /**
     * @throws Exception
     */
    public function testInitialiseModelFailsComposerNoNamespace() : void
    {
        $this->setProperty($this->services->paths(), 'root', '/opt/project/tests/Unit/Mocks/ComposerNoNamespace');

        $this->expectExceptionCode(ConfigurationException::ERROR_NAMESPACE_NOT_CONFIGURED);
        $this->controller->initialiseModel('modelName');
    }

    /**
     * @throws Exception
     */
    public function testInitialiseModel() : void
    {
        $this->controller->initialiseParameters([
                'requiredEncryptedParameter' => 1,
                'intParameter' => '123'
            ]
        );
        $this->setProperty($this->services->paths(), 'root', '/opt/project/tests/Unit/Mocks/MockComposer');
        $this->controller->initialiseModel('GenericModel');

        $this->assertEquals(1,1);
    }

    /**
     * @throws Exception
     */
    public function testInitialiseNonExistingModel() : void
    {
        $this->setProperty($this->services->paths(), 'root', '/opt/project/tests/Unit/Mocks/MockComposer');

        $this->expectExceptionCode(404);
        $this->controller->initialiseModel('NonExistingModel');
    }

    /**
     * @throws Exception
     */
    public function testGETParameters() : void
    {
        $_GET = [
            'requiredEncryptedParameter' => '1',
            'intParameter' => '123'
        ];
        $this->controller->initialiseParameters();

        $params = $this->getProperty($this->controller, 'passedParameters');
        $this->assertEquals($_GET, $params);
    }

    /**
     * @throws Exception
     */
    public function testPOSTPUTDELETEParameters() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'requiredEncryptedParameter' => '1',
            'intParameter' => '123'
        ];
        $this->controller->initialiseParameters();

        $this->assertEquals($_POST, $this->getProperty($this->controller, 'bodyParameters'));
    }

    /**
     * @throws Exception
     */
    public function testPhpInputParameters() : void
    {
        $array = [
            'param1' => 'uno'
        ];

        $json = json_encode($array, JSON_THROW_ON_ERROR);

        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $this->setProperty($this->controller, 'phpInput', $json);

        $this->controller->initialiseParameters();

        $this->assertEquals($array, $this->getProperty($this->controller, 'bodyParameters'));
    }

    /**
     * @throws Exception
     */
    public function testFailedPhpInputParameters() : void
    {
        $array = [
            'param1' => 'uno'
        ];

        $json = json_encode($array, JSON_THROW_ON_ERROR);

        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $this->setProperty($this->controller, 'phpInput', $json . ';123');

        $this->controller->initialiseParameters();

        $this->assertEquals([], $this->getProperty($this->controller, 'bodyParameters'));
    }

    /**
     * @throws Exception
     */
    public function testFileInput() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $_FILES = ['FirstName'=>['uno'=>'1']];

        $this->controller->initialiseParameters();

        $this->assertEquals(['uno'=>'1'], $this->getProperty($this->controller, 'file'));
    }

    /**
     * @throws Exception
     */
    public function testModelRedirect() : void
    {
        $this->controller->initialiseParameters([
            'requiredEncryptedParameter' => '1',
            'intParameter' => '123'
        ]);

        $this->setProperty($this->services->paths(), 'root', '/opt/project/tests/Unit/Mocks/MockComposer');
        $this->controller->initialiseModel('SecondGenericModel');

        $this->assertEquals('GenericModel', $this->getProperty($this->controller, 'modelName'));
    }

    /**
     * @throws Exception
     */
    public function testParseUriParameters() : void
    {
        $this->setProperty($this->services->paths(), 'root', '/opt/project/tests/Unit/Mocks/MockComposer');

        $_SERVER['REQUEST_URI'] = '/Subfolder/10/ThirdGenericModel';
        $this->controller->initialiseParameters();

        $this->assertEquals('Subfolder/ThirdGenericModel', $this->getProperty($this->controller, 'modelName'));
    }

    /**
     * @throws Exception
     */
    public function testParseUriParametersIndex() : void
    {
        $this->setProperty($this->services->paths(), 'root', '/opt/project/tests/Unit/Mocks/MockComposer');

        $_SERVER['REQUEST_URI'] = '10';
        $this->controller->initialiseParameters();

        $this->assertEquals('index', $this->getProperty($this->controller, 'modelName'));
    }

    /**
     * @throws Exception
     */
    public function testCompleteRender() : void
    {
        $this->controller->initialiseParameters([
            'requiredEncryptedParameter' => '1'
        ]);

        $this->setProperty($this->services->paths(), 'root', '/opt/project/tests/Unit/Mocks/MockComposer');
        $this->controller->initialiseModel('GenericModel');

        $this->controller->completeRender(200, 'Result');

        $this->assertEquals(1,1);
    }


}