<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\JsonApi\Events;

use CarloNicora\Minimalism\Core\JsonApi\Events\JsonApiInfoEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use function json_decode;

class JsonApiInfoEventsTest extends AbstractTestCase
{

    public function testTwigEngineInitialised()
    {
        $infoEvents = JsonApiInfoEvents::TWIG_ENGINE_INITIALISED();
        $this->assertEquals(ResponseInterface::HTTP_STATUS_500, $infoEvents->getHttpStatusCode());

        $message = json_decode($infoEvents->generateMessage(), true);
        unset($message['time']);
        $this->assertEquals(
            [ 'service' => 'module-jsonapi', 'id' => 1, 'details' => 'Twig engine initialised'],
            $message
        );
    }

    public function testPreRenderCompleted()
    {
        $infoEvents = JsonApiInfoEvents::PRE_RENDER_COMPLETED();
        $this->assertEquals(ResponseInterface::HTTP_STATUS_500, $infoEvents->getHttpStatusCode());

        $message = json_decode($infoEvents->generateMessage(), true);
        unset($message['time']);
        $this->assertEquals(
            [ 'service' => 'module-jsonapi', 'id' => 2, 'details' => 'Pre render completed successfully'],
            $message
        );
    }

    public function testDataGenerated()
    {
        $infoEvents = JsonApiInfoEvents::DATA_GENERATED();
        $this->assertEquals(ResponseInterface::HTTP_STATUS_500, $infoEvents->getHttpStatusCode());

        $message = json_decode($infoEvents->generateMessage(), true);
        unset($message['time']);
        $this->assertEquals(
            [ 'service' => 'module-jsonapi', 'id' => 3, 'details' => 'Data generated successfully'],
            $message
        );
    }

    public function testDataMerged()
    {
        $infoEvents = JsonApiInfoEvents::DATA_MERGED('test_view');
        $this->assertEquals(ResponseInterface::HTTP_STATUS_500, $infoEvents->getHttpStatusCode());

        $message = json_decode($infoEvents->generateMessage(), true);
        unset($message['time']);
        $this->assertEquals(
            [ 'service' => 'module-jsonapi', 'id' => 4, 'details' => 'Data merged with view test_view'],
            $message
        );
    }

    public function testRenderComplete()
    {
        $infoEvents = JsonApiInfoEvents::RENDER_COMPLETE();
        $this->assertEquals(ResponseInterface::HTTP_STATUS_500, $infoEvents->getHttpStatusCode());

        $message = json_decode($infoEvents->generateMessage(), true);
        unset($message['time']);
        $this->assertEquals(
            [ 'service' => 'module-jsonapi', 'id' => 5, 'details' => 'Render complete'],
            $message
        );
    }
}
