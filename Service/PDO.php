<?php
namespace PrimPack\Service;

class PDO extends \PDO {
    protected $PDO;
    public $numExecutes;
    public $numStatements;
    public $lastQuery;
    public $lastParams;

    public function __construct($dsn, $user = '', $pass = '', $driver_options = '') {
        $this->PDO = new \PDO($dsn, $user, $pass, $driver_options);
        $this->numExecutes = 0;
        $this->numStatements = 0;
    }
    public function __call($func, $args) {
        return call_user_func_array([&$this->PDO, $func], $args);
    }

    public function prepare($statement, $options = NULL) {
        $this->numStatements++;

        $this->lastQuery = $statement;

        $args = func_get_args();
        $PDOS = call_user_func_array([&$this->PDO, 'prepare'], $args);

        return new PDOStatement($this, $PDOS);
    }

    public function query() {
        $this->numExecutes++;
        $this->numStatements++;

        $args = func_get_args();

        $this->lastQuery = $args;
        $PDOS = call_user_func_array([&$this->PDO, 'query'], $args);

        return new PDOStatement($this, $PDOS);
    }

    public function exec($query) {
        $this->numExecutes++;
        $this->numStatements++;

        $args = func_get_args();

        $this->lastQuery = $args;

        return call_user_func_array([&$this->PDO, 'exec'], $args);
    }

    public function lastInsertId($name = null)
    {
        return $this->PDO->lastInsertId($name);
    }

    public function getAttribute($attribute)
    {
        return $this->PDO->getAttribute($attribute);
    }
}