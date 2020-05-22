<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractCliController;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractCliControllerTest extends AbstractTestCase
{
    /** @var MockObject|AbstractCliController */
    protected MockObject $controller;

    /**
     * @throws Exception
     */
    public function testInitialiseParameters() : void
    {
        $this->controller = $this
            ->getMockForAbstractClass(AbstractCliController::class, [$this->services]);

        $_SERVER['argv'] = [
            '-user',
            'Carlo',
            '--password',
            'pwd'
        ];

        $this->controller->initialiseParameters();

        $passedParameters = $this->getProperty($this->controller, 'passedParameters');
        $expectedParameters = ['user' => 'Carlo', 'password' => 'pwd'];

        $this->assertEquals($expectedParameters, $passedParameters);
    }

    /**
     * @throws Exception
     */
    public function testInitialiseJsonParameters() : void
    {
        $this->controller = $this
            ->getMockForAbstractClass(AbstractCliController::class, [$this->services]);

        $expectedParameters = ['user' => 'Carlo', 'password' => 'pwd'];

        $_SERVER['argv'] = [];
        $_SERVER['argv'][] = json_encode($expectedParameters, JSON_THROW_ON_ERROR);

        $this->controller->initialiseParameters();

        $passedParameters = $this->getProperty($this->controller, 'passedParameters');


        $this->assertEquals($expectedParameters, $passedParameters);
    }
}