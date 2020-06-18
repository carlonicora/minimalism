<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use ReflectionClass;
use ReflectionMethod;
use function ob_get_clean;
use function ob_start;

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
        ob_start();
        $this->instance->write();
        $output = ob_get_clean();

        $this->assertEquals($this->data, $output);
    }

    public function testWriteDoesntOutputDataOn204()
    {
        $this->instance->setStatus(ResponseInterface::HTTP_STATUS_204);
        ob_start();
        $this->instance->write();
        $output = ob_get_clean();
        $this->assertEmpty($output);
    }

    public function testWriteDoesntOutputDataOn304()
    {
        $this->instance->setStatus(ResponseInterface::HTTP_STATUS_304);
        ob_start();
        $this->instance->write();
        $output = ob_get_clean();
        $this->assertEmpty($output);
    }

    public function testWriteOutputForAllOtherStatusCodes()
    {
        foreach ($this->statusCodes() as $statusCode) {
            if ($statusCode === ResponseInterface::HTTP_STATUS_204 || $statusCode === ResponseInterface::HTTP_STATUS_304) {
                continue;
            }

            $this->instance->setStatus($statusCode);
            ob_start();
            $this->instance->write();
            $output = ob_get_clean();

            $this->assertEquals($this->data, $output);
        }
    }

    public function testGenerateStatusTextForAllStatusCodes()
    {
        $rm = new ReflectionMethod($this->instance, 'generateStatusText');
        $rm->setAccessible(true);

        foreach ($this->statusCodes() as $statusCode) {
            $this->instance->setStatus($statusCode);
            $this->assertNotEmpty($rm->invoke($this->instance));
        }
    }

    protected function statusCodes()
    {
        $rc = new ReflectionClass(ResponseInterface::class);
        $constants = $rc->getConstants();
        foreach($constants as $constantName => $constantValue) {
            yield $constantValue;
        }
    }


    public function testWriteProtocolForAllStatusCodes()
    {
        $mock = $this->getMockBuilder(Response::class)->onlyMethods(['writeRawHTTP'])->getMock();
        foreach ($this->statusCodes() as $k => $statusCode) {
            $mock->expects($this->at($k))->method('writeRawHTTP')->with(
                $this->callback(function($protocol) use($statusCode) {
                    return strpos($protocol, "HTTP/1.1 $statusCode") !== false;
                })
            );
        }

        foreach ($this->statusCodes() as $statusCode) {
            $mock->setStatus($statusCode);
            $mock->writeProtocol();
        }
    }

    public function testWriteContentType()
    {
        $mock = $this->getMockBuilder(Response::class)->onlyMethods(['writeRawHTTP'])->getMock();
        $mock->expects($this->at(0))->method('writeRawHTTP')->with('Content-Type: text/html');
        $mock->expects($this->at(1))->method('writeRawHTTP')->with('Content-Type: text/plain');

        $mock->writeContentType();
        $mock->setContentType('text/plain');
        $mock->writeContentType();
    }


    public function testRedirectionCodePath()
    {
        $redirectionParameters = ['test1' => 'value', 'test2' => 'value'];

        $this->instance->setRedirectionParameters($redirectionParameters);
        $this->assertEquals($redirectionParameters, $this->instance->getRedirectionParameters());

        $this->assertNull($this->instance->redirects());
        $this->instance->setRedirect('Test');
        $this->assertEquals('Test', $this->instance->redirects());

    }
}
