<?php
namespace CarloNicora\Minimalism\Tests\Unit\Services\Logger;

use CarloNicora\Minimalism\Core\Events\MinimalismInfoEvents;
use CarloNicora\Minimalism\Services\Logger\Logger;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use Exception;
use JsonException;
use Throwable;

class LoggerTest extends AbstractTestCase
{
    /** @var Logger  */
    public Logger $logger;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        if (false === getenv('MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY')) {
            putenv("MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY=true");
        }
        if (!isset($_ENV['MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY'])) {
            $_ENV['MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY'] = 'true';
        }

        $this->logger = $this->services->service(Logger::class);

        unset($_ENV['MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY']);
    }

    public function testAddEvent() : void
    {
        $this->setProperty($this->logger->info(), 'events', []);
        $this->logger->info()->log(MinimalismInfoEvents::PARAMETERS_VALIDATED());

        /** @var array $events */
        $events = $this->getProperty($this->logger->info(), 'events');
        $eventsCount = count($events);

        $this->assertEquals(1, $eventsCount);
    }

    /**
     * @throws JsonException
     */
    public function testMulitpleInfo() : void
    {
        $this->setProperty($this->logger->info(), 'events', []);
        $this->logger->info()->log(MinimalismInfoEvents::PARAMETERS_VALIDATED());
        $this->logger->info()->log(MinimalismInfoEvents::PARAMETERS_VALIDATED());
        $this->logger->info()->log(MinimalismInfoEvents::PARAMETERS_VALIDATED());

        $this->logger->info()->flush();

        $this->assertEquals(1,1);
    }

    public function testInfoLogger() : void
    {
        $this->setProperty($this->logger->info(), 'events', []);
        $log = $this->logger->info()->log(MinimalismInfoEvents::PARAMETERS_VALIDATED());

        $this->assertEquals('7', $log->getMessageCode());
    }

    /**
     * @throws Throwable
     */
    public function testInfoThrowException() : void
    {
        $this->setProperty($this->logger->info(), 'events', []);

        $this->expectExceptionCode(500);

        $this->logger->info()->log(MinimalismInfoEvents::PARAMETERS_VALIDATED())->throw();
    }
}