<?php

namespace App\Josel\Configs;

/**
 * This class describes a dev.
 */
class Dev extends Base implements Interfaces\Config
{
    /**
     * Initializes the configuration.
     */
    public function initConfig()
    {
        $database = $this->getData('database');
        $database->setDatabase('test');
        $database->setUser('root');
        $database->setPassword('root');
    }
}
