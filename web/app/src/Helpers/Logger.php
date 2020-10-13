<?php

namespace App\Josel\Helpers;

use App\Josel\Core\Singleton;

/**
 * This class describes a logger.
 */
class Logger extends Singleton
{
    public $last_log = '';

    /**
     * Logs the error message
     *
     * @param      string  $message  The message
     */
    public static function errorLog($message)
    {
        $logger = self::getInstance();
        $logger->log($message);
    }

    /**
     * Logs the error message
     *
     * @param      string  $message  The message
     */
    public function log($message)
    {
        $test_suit = getenv('PHPUNIT_APP_JOSEL_TESTSUITE');
        if ($test_suit == "true") {
            $message = "PHPUNIT INFO LOG: " . $message;
            $this->last_log = $message;
            echo "\n" . $message;
        } else {
            $this->last_log = $message;
            error_log($message);
        }
    }
}
