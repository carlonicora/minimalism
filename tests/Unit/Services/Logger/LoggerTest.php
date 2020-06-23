<?php


namespace CarloNicora\Minimalism\Tests\Unit\Services\Logger;


use CarloNicora\Minimalism\Services\Logger\Configurations\LoggerConfigurations;
use CarloNicora\Minimalism\Services\Logger\LogBuilders\ErrorLogBuilder;
use CarloNicora\Minimalism\Services\Logger\LogBuilders\InfoLogBuilder;
use CarloNicora\Minimalism\Services\Logger\Logger;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class LoggerTest extends AbstractTestCase
{

    public function testLoggerInstance()
    {
        $logger = new Logger(new LoggerConfigurations(), $this->getServices());
        $this->assertInstanceOf(ErrorLogBuilder::class, $logger->error());
        $this->assertInstanceOf(InfoLogBuilder::class, $logger->info());
    }
}
