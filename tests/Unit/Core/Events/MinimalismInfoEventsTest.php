<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Events;

use CarloNicora\Minimalism\Core\Events\MinimalismInfoEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use function json_decode;

class MinimalismInfoEventsTest extends AbstractTestCase
{

    public function testServicesInitialised()
    {
        $instance = MinimalismInfoEvents::SERVICES_INITIALISED();

        self::assertEquals('5', $instance->getMessageCode());
        self::assertEquals(ResponseInterface::HTTP_STATUS_500, $instance->getHttpStatusCode());
        self::assertEquals(
            'Services initialised from scratch',
            json_decode($instance->generateMessage(), true)['details']
        );
    }
}
