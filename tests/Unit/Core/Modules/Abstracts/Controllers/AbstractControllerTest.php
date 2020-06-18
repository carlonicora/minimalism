<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractController;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Interfaces\SecurityInterface;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use function array_merge;
use function json_encode;

class AbstractControllerTest extends AbstractTestCase
{
    private MockObject $instance;

    public function setUp(): void
    {
        $this->instance = $this->getMockBuilder(AbstractController::class)
            ->setConstructorArgs([$this->getServices()])
            ->onlyMethods([
                'parseUriParameters',
                'getPhpInputParameters'
            ])
            ->getMockForAbstractClass();
    }

    public function testInitialiseParametersWithDefault()
    {
        $this->instance->expects($this->once())->method('parseUriParameters');
        $this->instance->expects($this->once())->method('getPhpInputParameters');

        /** @noinspection PhpUndefinedMethodInspection */
        $this->instance->initialiseParameters();
    }


    /**
     * @noinspection PhpUndefinedMethodInspection
     */
    public function testInitialiseParametersWithEitherValueArrays()
    {
        $this->instance->expects($this->never())->method('parseUriParameters');
        $this->instance->expects($this->never())->method('getPhpInputParameters');

        $this->instance->initialiseParameters([1]);
        $this->assertEquals([1], $this->getProperty($this->instance, 'passedParameters'));
        $this->assertEquals([], $this->getProperty($this->instance, 'bodyParameters'));

        $this->instance->initialiseParameters([], [1]);
        $this->assertEquals([], $this->getProperty($this->instance, 'passedParameters'));
        $this->assertEquals([1], $this->getProperty($this->instance, 'bodyParameters'));
    }


    /**
     * @note HTTP type defaults to GET
     * @see AbstractController::getHttpType()
     */
    public function testInitialiseParametersWithGETData()
    {
        global $_GET;
        $_GET['path'] = 'ignored';
        $_GET['XDEBUG_SESSION_START'] = 'ignored';
        $_GET['test'] = 'test value';

        /** @noinspection PhpUndefinedMethodInspection */
        $this->instance->initialiseParameters();
        $this->assertEquals(['test' => 'test value'], $this->getProperty($this->instance, 'passedParameters'));

        unset($_GET['path']);
        unset($_GET['XDEBUG_SESSION_START']);
        unset($_GET['test']);
    }


    public function methods()
    {
        return [
            ['POST'],
            ['PUT'],
            ['DELETE']
        ];
    }

    /**
     * @dataProvider methods
     * @noinspection PhpUndefinedMethodInspection
     * @param $method
     */
    public function testInitialiseParametersWithNonGetData($method)
    {
        global $_SERVER;
        global $_POST;
        global $_FILES;

        $_SERVER['REQUEST_METHOD'] = $method;

        $testArrayUsedAsJSON = [
            'test1' => 'test value1',
            'test2' => 'test value2',
            'test3' => 'test value2'
        ];
        $this->setProperty($this->instance, 'phpInput', json_encode($testArrayUsedAsJSON));

        $_POST['test3'] = 'different test value';

        $_FILES = [
            [ 'test1' => 'test value' ],
            [ 'test2' => 'test value' ]
        ];

        $this->instance->initialiseParameters();
        $this->assertEquals(
            array_merge($testArrayUsedAsJSON, $_POST),
            $this->getProperty($this->instance, 'bodyParameters')
        );

        /** file is only populated if a single file is uploaded */
        $this->assertEquals(null, $this->getProperty($this->instance, 'file'));

        $_FILES = [ [ 'test1' => 'test value' ] ];
        $this->instance->initialiseParameters();
        $this->assertEquals($_FILES[0], $this->getProperty($this->instance, 'file'));

        unset($_SERVER['REQUEST_METHOD']);
        unset($_POST['test3']);
        $_FILES = [];
    }


    public function testInitialiseModelWithDefaults()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Model not found: index');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->instance->initialiseModel();
    }


    public function testInitialiseModelWithModelInstanceAndDefaults()
    {
        $mockModel = $this->getMockBuilder(ModelInterface::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();

        $mockModel->expects($this->once())->method('setVerb')->with('GET');
        $mockModel->expects($this->once())->method('setEncrypter')->with(null);
        $mockModel->expects($this->once())->method('initialise')->with([], null);
        $mockModel->expects($this->once())->method('redirect')->with()->willReturn('');

        /** @noinspection PhpUndefinedMethodInspection */
        $this->instance->initialiseModel($mockModel);
    }


    public function testInitialiseModelWithModelRedirection()
    {
        $initialModel = $this->getMockBuilder(ModelInterface::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();
        $finalModel = $this->getMockBuilder(ModelInterface::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();

        $initialModel->expects($this->exactly(2))->method('redirect')->with()->willReturn($finalModel);
        $finalModel->expects($this->once())->method('setVerb')->with('GET');
        $finalModel->expects($this->once())->method('setEncrypter')->with(null);
        $finalModel->expects($this->once())->method('initialise')->with([], null);
        $finalModel->expects($this->once())->method('redirect')->with()->willReturn('');

        /** @noinspection PhpUndefinedMethodInspection */
        $this->instance->initialiseModel($initialModel);
    }


    public function testSetSecurityInterface()
    {
        $mock = $this->getMockBuilder(SecurityInterface::class)->getMock();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->instance->setSecurityInterface($mock);
        $this->assertSame($mock, $this->getProperty($this->instance, 'security'));
    }


    public function testSetEncrypterInterface()
    {
        $mock = $this->getMockBuilder(EncrypterInterface::class)->getMock();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->instance->setEncrypterInterface($mock);
        $this->assertSame($mock, $this->getProperty($this->instance, 'encrypter'));
    }
}

