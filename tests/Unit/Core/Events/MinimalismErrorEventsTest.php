<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Events;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;
use function json_decode;

/**
 * Class MinimalismErrorEventsTest
 * @package CarloNicora\Minimalism\Tests\Unit\Core\Events
 *
 * Adds additional test cases for the MinimalismErrorEvents that are not covered by the other test cases
 */
class MinimalismErrorEventsTest extends AbstractTestCase
{

    public function testCookieSettingError()
    {
        $instance = MinimalismErrorEvents::COOKIE_SETTING_ERROR(new Exception());

        self::assertEquals('3', $instance->getMessageCode());
        self::assertEquals(ResponseInterface::HTTP_STATUS_500, $instance->getHttpStatusCode());
        self::assertEquals(
            'Services could not be saved in the cookies',
            json_decode($instance->generateMessage(), true)['error']
        );
    }

    public function testConfigurationError()
    {
        $instance = MinimalismErrorEvents::CONFIGURATION_ERROR('test');

        self::assertEquals('4', $instance->getMessageCode());
        self::assertEquals(ResponseInterface::HTTP_STATUS_500, $instance->getHttpStatusCode());
        self::assertEquals(
            'Error in the environment configuration: test',
            json_decode($instance->generateMessage(), true)['error']
        );
    }

    public  function testModuleNotLoaded()
    {
        $instance = MinimalismErrorEvents::MODULE_NOT_LOADED();

        self::assertEquals('7', $instance->getMessageCode());
        self::assertEquals(ResponseInterface::HTTP_STATUS_500, $instance->getHttpStatusCode());
        self::assertEquals(
            'No module configured',
            json_decode($instance->generateMessage(), true)['error']
        );
    }

    public function testMultipleModulesLoaded()
    {
        $instance = MinimalismErrorEvents::MULTIPLE_MODULES_LOADED();

        self::assertEquals('8', $instance->getMessageCode());
        self::assertEquals(ResponseInterface::HTTP_STATUS_500, $instance->getHttpStatusCode());
        self::assertEquals(
            'Multiple modules configured',
            json_decode($instance->generateMessage(), true)['error']
        );
    }

    public function testFileWriteError()
    {
        $instance = MinimalismErrorEvents::FILE_WRITE_ERROR('/tmp/test');

        self::assertEquals('18', $instance->getMessageCode());
        self::assertEquals(ResponseInterface::HTTP_STATUS_500, $instance->getHttpStatusCode());
        self::assertEquals(
            'File write failed: /tmp/test',
            json_decode($instance->generateMessage(), true)['error']
        );
    }
}
