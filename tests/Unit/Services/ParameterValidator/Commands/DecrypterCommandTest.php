<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Commands;

use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Commands\DecrypterCommand;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class DecrypterCommandTest extends AbstractTestCase
{

    public function testDecryptParameterWithoutEncrypter()
    {
        $instance = new DecrypterCommand(null);

        $this->assertEquals(1, $instance->decryptParameter('1'));
        $this->assertEquals(0, $instance->decryptParameter('text'));
    }


    public function testDecryptParameterWithEncrypter()
    {
        $parameter = 'sample parameter';
        $mock = $this->getMockBuilder(EncrypterInterface::class)->getMock();

        $mock->expects($this->once())->method('decryptId')->with($parameter)->willReturn(0);

        $instance = new DecrypterCommand($mock);
        $this->assertEquals(0, $instance->decryptParameter($parameter));
    }
}
