<?php

namespace App\Josel\Configs;

use App\Josel\Core\VarienObject;

/**
 * This class describes a dev.
 */
class Base extends VarienObject
{
    public function __construct()
    {
        parent::__construct();
        $this->setData('database', new VarienObject(array(
            'host'     => 'mysql',
            'database' => 'default',
            'user'     => 'root',
            'password' => 'root',
        )));

        $this->initConfig();
    }
}
