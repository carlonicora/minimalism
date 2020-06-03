<?php

namespace CarloNicora\Minimalism\Tests\Core;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class ResponseTest extends AbstractTestCase
{

    /**
     * @var Response
     */
    private Response $instance;
    private string $data = 'test data';

    public function setUp(): void
    {
        $this->instance = new Response();
        $this->instance->setData($this->data);
    }

    public function testWriteOutputWhenNotHttpResponse()
    {
        $this->instance->setNotHttpResponse();
        \ob_start();
        $this->instance->write();
        $output = \ob_get_clean();

        $this->assertEquals($this->data, $output);
    }

    public function testWriteDoesntOutputDataOn204()
    {
        $this->instance->setStatus(ResponseInterface::HTTP_STATUS_204);
        \ob_start();
        $this->instance->write();
        $output = \ob_get_clean();
        $this->assertEmpty($output);
    }

    public function testWriteDoesntOutputDataOn304()
    {
        $this->instance->setStatus(ResponseInterface::HTTP_STATUS_204);
        \ob_start();
        $this->instance->write();
        $output = \ob_get_clean();
        $this->assertEmpty($output);
    }

    public function testWriteOutputForAllOtherStatusCodes()
    {
        foreach ($this->statusCodes() as $statusCode) {
            if ($statusCode === ResponseInterface::HTTP_STATUS_204 || $statusCode === ResponseInterface::HTTP_STATUS_304) {
                continue;
            }

            $this->instance->setStatus($statusCode);
            \ob_start();
            $this->instance->write();
            $output = \ob_get_clean();

            $this->assertEquals($this->data, $output);
        }
    }

    public function testGenerateStatusTextForAllStatusCodes()
    {
        $rm = new \ReflectionMethod($this->instance, 'generateStatusText');
        $rm->setAccessible(true);

        foreach ($this->statusCodes() as $statusCode) {
            $this->instance->setStatus($statusCode);
            $this->assertNotEmpty($rm->invoke($this->instance));
        }
    }

    public function statusCodes()
    {
        $rc = new \ReflectionClass(ResponseInterface::class);
        $constants = $rc->getConstants();
        foreach($constants as $constant) {
            yield $rc->getConstant($constant);
        }
    }
}
