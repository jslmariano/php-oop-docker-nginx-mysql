<?php

namespace App\Test\Configs;

use App\Josel\Configs\Factories\Config;
use PHPUnit\Framework\TestCase;
use App\Josel\Configs\Staging;
use App\Josel\Configs\Prod;
use App\Josel\Configs\Dev;
/**
 * This class describes a load configuration.
 */
class LoadConfigTest extends TestCase
{
    /**
     * Original env variable
     */
    public $original_env;

    public function setUp(): void
    {
        $this->original_env = getenv('ENV');
    }

    public function tearDown(): void
    {
        putenv("ENV=$this->original_env");
    }

    /**
     * test load staging config
     */
    public function test_get_config_staging()
    {
        putenv("ENV=STAGING");
        $config = Config::getConfig();
        $this->assertInstanceOf(Staging::class, $config);
    }

    /**
     * test load dev config
     */
    public function test_get_config_dev()
    {
        putenv("ENV=DEV");
        $config = Config::getConfig();
        $this->assertInstanceOf(Dev::class, $config);
    }

    /**
     * test load prod config
     */
    public function test_get_config_prod()
    {
        putenv("ENV=PROD");
        $config = Config::getConfig();
        $this->assertInstanceOf(Prod::class, $config);
    }

    /**
     * test load default config
     */
    public function test_get_config_default()
    {
        putenv("ENV=''");
        $config = Config::getConfig();
        $this->assertInstanceOf(Dev::class, $config);
    }
}

