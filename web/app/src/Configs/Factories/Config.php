<?php

namespace App\Josel\Configs\Factories;

use App\Josel\Configs\Dev;
use App\Josel\Configs\Staging;
use App\Josel\Configs\Prod;

/**
 * This interface describes a configuration factory.
 */
class Config
{
    const DEVELOPMENT = 'dev';
    const STAGING     = 'staging';
    const PRODUCTION  = 'prod';

    /**
     * Gets the configuration.
     *
     * @return     The configuration.
     */
    public static function getConfig()
    {
        $env = getenv('ENV');
        $env = trim($env);
        $env = strtolower($env);

        $config = null;

        switch ($env) {
            case Config::DEVELOPMENT:
                $config = new Dev;
                break;
            case Config::STAGING:
                $config = new Staging;
                break;
            case Config::PRODUCTION:
                $config = new Prod;
                break;
            default:
                $config = new Dev;
                break;
        }

        return $config;
    }
}
