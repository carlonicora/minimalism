<?php
namespace CarloNicora\Minimalism\Tests\Unit\Services\Logger;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Factories\ServiceFactory;
use CarloNicora\Minimalism\Services\Logger\Logger;
use CarloNicora\Minimalism\Services\Logger\Objects\Log;
use CarloNicora\Minimalism\Services\Paths\Paths;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use PHPUnit\Framework\Constraint\Count;

class LoggerTest extends AbstractTestCase
{
    /** @var Logger  */
    public Logger $logger;

    public function setUp(): void
    {
        parent::setUp();

        if (false === getenv('MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY')) {
            putenv("MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY=true");
        }
        if (!isset($_ENV['MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY'])) {
            $_ENV['MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY'] = 'true';
        }

        $services = new ServicesFactory();
        $services->loadService(ServiceFactory::class);

        $this->logger = $services->service(Logger::class);

        unset($_ENV['MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY']);
    }

    public function testAddSystemEvent() : void
    {
        $this->logger->addSystemEvent(new Log('message'));
        $this->logger->addSystemEvent(null,'message');

        /** @var array $events */
        $events = $this->getProperty($this->logger, 'events');
        $eventsCount = count($events);

        $this->assertEquals(2, $eventsCount);
    }

    public function testAddEvent() : void
    {
        $this->logger->addEvent('message');

        /** @var array $events */
        $events = $this->getProperty($this->logger, 'events');
        $eventsCount = count($events);

        $this->assertEquals(1, $eventsCount);
    }

    public function testFlush() : void
    {
        $this->logger->addEvent('message');
        $this->logger->flush();

        /** @var array $events */
        $events = $this->getProperty($this->logger, 'events');
        $eventsCount = count($events);

        $this->assertEquals(0, $eventsCount);
    }
}