<?php

namespace CarloNicora\Minimalism\Tests\Unit\Modules\Api;

use CarloNicora\Minimalism\Modules\Api\ApiController;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;

class ApiControllerTest extends AbstractTestCase
{

    public function testInitialiseModelWithDefaults()
    {
        $instance = new ApiController($this->getServices());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Model not found: index');
        $instance->initialiseModel();
    }
}
