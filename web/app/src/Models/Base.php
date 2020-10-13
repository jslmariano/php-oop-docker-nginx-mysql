<?php

namespace App\Josel\Models;

use App\Josel\Core\DBManager;
use App\Josel\Core\VarienObject;

/**
 * This class describes a base model.
 */
class Base
{
    /**
     * @var App\Josel\Core\DBManager
     */
    protected $db_manager;

    /**
     * Table name
     *
     * @var        string
     */
    protected $table = null;

    /**
     * Datas of model should contain value key pair of column => data pair
     */
    public $objects;

    /**
     * Series of objects
     */
    public $collection;

    public function __construct()
    {
        $this->objects    = new VarienObject();
        $this->initCollection();
    }

    /**
     * Gets the database manager
     *
     * @return     DBManager  The database connection.
     */
    public function getDbManager()
    {
        if (empty($this->db_manager)) {
            $this->db_manager = DBManager::getInstance();
        }

        return $this->db_manager;
    }

    /**
     * Initializes the collection.
     */
    public function initCollection()
    {
        $this->collection = new VarienObject();
        return $this;
    }

    /**
     * Gets the objects.
     *
     * @return     VarienObject  The objects.
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Gets the Collection.
     *
     * @return     VarienObject  The Collection.
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Adds to collection.
     *
     * @param      mixed  $object  The object
     */
    public function addToCollection($object)
    {
        $length = 0;
        if ($this->getCollection()->hasData()) {
            $length = count($this->collection->toArray());
        }
        $this->getCollection()->setData($length, new VarienObject($object));
        return $this;
    }

    /**
     * Gets the table.
     *
     * @return     string  The table.
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Saves a record
     *
     * @return     self  model instance
     */
    public function save()
    {
        $column_datas  = $this->getObjects()->getData();

        $column_names = array_keys($column_datas);
        $column_names = implode(",", $column_names);

        $value_fill = array_fill(0, count($column_datas), '?');
        $value_fill = implode(',', $value_fill);

        $insert_query = "INSERT INTO %s (%s) VALUES (%s);";
        $query = sprintf(
            $insert_query,
            $this->getTable(),
            $column_names,
            $value_fill
        );

        $this->getDbManager()->runQuery(
            $query,
            array_values($column_datas)
        );

        return $this;
    }
}
