<?php

namespace App\Test\Helpers;

use PHPUnit\Framework\TestCase;
use App\Josel\Helpers\Logger;
/**
 * This class describes a load configuration.
 */
class LoggerTest extends TestCase
{
    public function tearDown(): void
    {
        putenv("PHPUNIT_APP_JOSEL_TESTSUITE=true");
    }

    /**
     * Test log outside phpunit
     */
    public function test_logger_outside_phpunit()
    {
        putenv("PHPUNIT_APP_JOSEL_TESTSUITE=false");
        Logger::errorLog("Test Log");
        $logger_instance = Logger::getInstance();
        $this->assertEquals("Test Log", $logger_instance->last_log);
    }

    /**
     * Test log inside phpunit
     */
    public function test_logger_inside_phpunit()
    {
        putenv("PHPUNIT_APP_JOSEL_TESTSUITE=true");
        Logger::errorLog("Test Log");
        $logger_instance = Logger::getInstance();
        $this->assertEquals("PHPUNIT INFO LOG: Test Log", $logger_instance->last_log);
    }
}
