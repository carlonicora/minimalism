<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core;

use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;

class ResponseTest extends AbstractTestCase
{
    private function generateResponse(string $httpCode=Response::HTTP_STATUS_200) : Response
    {
        $response = new Response();
        $response->setStatus($httpCode);
        $response->setData('html');

        return $response;
    }

    public function testContentType() : void
    {
        $response = $this->generateResponse();
        $response->setContentType('test');

        $response->writeContentType();

        /** @noinspection ForgottenDebugOutputInspection */
        /** @noinspection PhpUnitAssertContainsInspection */
        $this->assertContains('Content-Type: test', xdebug_get_headers());
    }

    public function testWrite() : void
    {
        $response = $this->generateResponse();

        $this->expectOutputString('html');

        $response->write();
    }

    public function testSetNoHttpResponse() : void
    {
        $response = $this->generateResponse();
        $response->setNotHttpResponse();
        $this->setProperty($response, 'data', 'noHttpResponse');

        $this->expectOutputString('noHttpResponse');

        $response->write();
    }

    public function testHttpResponseCode200() : void
    {
        $response = $this->generateResponse();

        $response->writeProtocol();

        $this->assertEquals(200, http_response_code());
    }

    public function testHttpResponseCode201() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_201);

        $this->expectOutputString('html');

        $response->write();

        $this->assertEquals(201, http_response_code());
    }

    public function testHttpResponseCode204() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_204);

        $this->expectOutputString('');

        $response->write();

        $this->assertEquals(204, http_response_code());
    }

    public function testHttpResponseCode205() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_205);

        $response->write();

        $this->assertEquals(205, http_response_code());
    }

    public function testHttpResponseCode304() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_304);

        $this->expectOutputString('');

        $response->write();

        $this->assertEquals(304, http_response_code());
    }

    public function testHttpResponseCode400() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_400);

        $response->write();

        $this->assertEquals(400, http_response_code());
    }

    public function testHttpResponseCode401() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_401);

        $response->write();

        $this->assertEquals(401, http_response_code());
    }

    public function testHttpResponseCode403() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_403);

        $response->write();

        $this->assertEquals(403, http_response_code());
    }

    public function testHttpResponseCode404() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_404);

        $response->write();

        $this->assertEquals(404, http_response_code());
    }

    public function testHttpResponseCode405() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_405);

        $response->write();

        $this->assertEquals(405, http_response_code());
    }

    public function testHttpResponseCode406() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_406);

        $response->write();

        $this->assertEquals(406, http_response_code());
    }

    public function testHttpResponseCode409() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_409);

        $response->write();

        $this->assertEquals(409, http_response_code());
    }

    public function testHttpResponseCode410() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_410);

        $response->write();

        $this->assertEquals(410, http_response_code());
    }

    public function testHttpResponseCode411() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_411);

        $response->write();

        $this->assertEquals(411, http_response_code());
    }

    public function testHttpResponseCode412() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_412);

        $response->write();

        $this->assertEquals(412, http_response_code());
    }

    public function testHttpResponseCode415() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_415);

        $response->write();

        $this->assertEquals(415, http_response_code());
    }

    public function testHttpResponseCode422() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_422);

        $response->write();

        $this->assertEquals(422, http_response_code());
    }

    public function testHttpResponseCode428() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_428);

        $response->write();

        $this->assertEquals(428, http_response_code());
    }

    public function testHttpResponseCode429() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_429);

        $response->write();

        $this->assertEquals(429, http_response_code());
    }

    public function testHttpResponseCode500() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_500);

        $response->write();

        $this->assertEquals(500, http_response_code());
    }

    public function testHttpResponseCode501() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_501);

        $response->write();

        $this->assertEquals(501, http_response_code());
    }

    public function testHttpResponseCode502() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_502);

        $response->write();

        $this->assertEquals(502, http_response_code());
    }


    public function testHttpResponseCode503() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_503);

        $response->write();

        $this->assertEquals(503, http_response_code());
    }

    public function testHttpResponseCode504() : void
    {
        $response = $this->generateResponse(Response::HTTP_STATUS_504);

        $response->write();

        $this->assertEquals(504, http_response_code());
    }
}