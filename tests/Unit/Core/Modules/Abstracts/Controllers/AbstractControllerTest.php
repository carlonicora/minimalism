<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractController;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;

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

        $this->instance->initialiseParameters();
    }


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
        $this->setProperty($this->instance, 'phpInput', \json_encode($testArrayUsedAsJSON));

        $_POST['test3'] = 'different test value';

        $_FILES = [
            [ 'test1' => 'test value' ],
            [ 'test2' => 'test value' ]
        ];

        $this->instance->initialiseParameters();
        $this->assertEquals(
            \array_merge($testArrayUsedAsJSON, $_POST),
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
}

