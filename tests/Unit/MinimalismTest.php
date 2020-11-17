<?php

namespace CarloNicora\Minimalism\Tests\Unit;

use CarloNicora\Minimalism\Minimalism;
use CarloNicora\Minimalism\Modules\Cli\CliModel;
use function ob_get_clean;
use function ob_start;

class MinimalismTest extends AbstractTestCase
{
    /*
    public function testWebExecutionWithDefault()
    {
        ob_start();
        Minimalism::executeWeb();
        $output = ob_get_clean();

        $this->assertEquals('Model not found: index', $output);
    }


    public function testApiExecutionWithDefault()
    {
        ob_start();
        Minimalism::executeApi();
        $output = ob_get_clean();

        $this->assertEquals('Model not found: index', $output);
    }
    */

    /**
     *
     */
    public function testCliExecutionWithModel(): void
    {
        $mock = $this->getMockBuilder(CliModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();
        $mock->method('redirect')->willReturn('');

        ob_start();
        Minimalism::executeCli($mock);
        $output = ob_get_clean();

        $this->assertEquals('', $output);
    }
}
