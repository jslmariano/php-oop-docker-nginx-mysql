<?php

namespace App\Josel\Core;

use mysqli;
use mysqli_report;
use mysqli_sql_exception;
use mysqli_connect_error;

use App\Josel\Configs\Factories\Config;
use App\Josel\Helpers\Logger as LoggerHelper;
use Exception;

/**
 * This class describes a code database manager
 */
class DBManager extends Singleton
{
    /**
     * Mysqli connection
     */
    protected $connection = null;

    /*
     * last error message
     */
    protected $last_error = null;

    /**
     * Query statements
     *
     * @var        array
     */
    public $statements = array();

    /**
     * Query strings
     *
     * @var        array
     */
    public $queries = array();

    /**
     * Current UID for statement
     */
    public $current_uid = null;

    /**
     * Constructs a new instance.
     */
    public function __construct()
    {
        $config = Config::getConfig();
        $this->initConnection(
            $config->getDatabase()->getHost(),
            $config->getDatabase()->getUser(),
            $config->getDatabase()->getPassword(),
            $config->getDatabase()->getDatabase()
        );
    }

    /**
     * Destroys the object and the conenction
     */
    public function __destruct()
    {
        if ($this->isConnected()) {
            $this->connection->close();
        }
    }

    /**
     * Initializes the connection.
     *
     * @param      string      $host      The db host
     * @param      string      $user      The db user
     * @param      string      $pass      The db pass
     * @param      string      $database  The db name
     *
     * @throws     \Exception  (description)
     */
    private function initConnection($host, $user, $pass, $database)
    {
        $this->connection = new mysqli($host, $user, $pass, $database);

        /* check connection */
        if (mysqli_connect_errno()) {
            $this->last_error = sprintf(
                "Connect failed: %s\n",
                mysqli_connect_error()
            );
            throw new Exception($this->last_error);
        }
    }

    /**
     * Determines if connected.
     *
     * @return     bool  True if connected, False otherwise.
     */
    public function isConnected()
    {
        return (boolean) $this->connection->connect_errno;
    }

    /**
     * Adds a statement.
     *
     * @param      string  $query   The query
     * @param      array   $params  The parameters
     */
    public function addStatement($query, $params = array())
    {
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param(
            str_repeat('s', count($params)),
            ...$params
        );
        $this->statements[] = $stmt;
        return $this;
    }

    /**
     * Gets the statements.
     *
     * @return     mixed  The statements.
     */
    public function getStatements()
    {
        return $this->statements;
    }

    /**
     * Gets the statament current result.
     *
     * @return     array  The current result.
     */
    public function getCurrentResult()
    {
        return $this->statements[$this->current_uid]->get_result();
    }

    /**
     * Runs the query
     *
     * @param      string  $query   The query
     * @param      array   $params  The parameters
     */
    public function runQuery($query, $params = array())
    {
        // Transform all errors to exceptions!
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $uid = uniqid();

        try {
            $stmt = $this->connection->prepare($query);

            $this->queries[$uid]           = array();
            $this->queries[$uid]['query']  = $query;
            $this->queries[$uid]['params'] = $params;
            $this->current_uid             = $uid;
            $this->statements[$uid]        = $stmt;
            $this->statements[$uid]->bind_param(
                str_repeat('s', count($params)),
                ...$params
            );
            $this->statements[$uid]->execute();
        } catch (mysqli_sql_exception $exception) {
            $this->last_error = $exception->getMessage();
            LoggerHelper::errorLog("ERROR while storing tip: ". (string) $this->last_error);
            throw $exception;
        }
    }

    /**
     * Begins a transaction.
     */
    public static function beginTransaction()
    {
        $manager = self::getInstance();
        $manager->connection->autocommit(false);
    }

    /**
     * Commit a transaction.
     */
    public static function commitTransaction()
    {
        // Transform all errors to exceptions!
        \mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $manager = self::getInstance();

        try {
            $manager->connection->commit();
        } catch (\mysqli_sql_exception $exception) {
            throw $exception;
        }
    }

    /**
     * Rollback a transaction.
     */
    public static function rollbackTransaction()
    {
        $manager = self::getInstance();
        if ($manager->isConnected()) {
            $manager->connection->rollback();
            foreach ($manager->getStatements() as $statement) {
                if (!$statement->num_rows) {
                    continue;
                }
                try {
                    $statement->close();
                } catch (Exception $e) {
                    /**
                     * Just let this pass cause if connection is closed then you will
                     * not be able to get statments from mysli_stmt()
                     */
                    continue;
                }
            }
            $manager->connection->autocommit(true);
        }
    }

    /**
     * Force close the connection
     */
    public static function forceClose()
    {
        $manager = self::getInstance();
        if ($manager->connection) {
            $manager->connection->close();
        }
    }

    /**
     * Renew connection
     */
    public static function reConnect()
    {
        $manager = self::getInstance();
        if (!$manager->isConnected()) {
            $manager->__construct();
        }
    }
}
