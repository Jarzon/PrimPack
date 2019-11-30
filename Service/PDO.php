<?php
namespace PrimPack\Service;

class PDO extends \PDO {
    protected $PDO;
    public int $numExecutes = 0;
    public int $numStatements = 0;
    public string $lastQuery = '';
    public PDOStatement $lastStatement;
    public array $lastParams;

    public function __construct($dsn, $user = '', $pass = '', $driver_options = []) {
        $driver_options += [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        $this->PDO = new \PDO($dsn, $user, $pass, $driver_options);
    }
    public function __call($func, $args) {
        return call_user_func_array([&$this->PDO, $func], $args);
    }

    public function prepare($query, $options = NULL) {
        $this->numStatements++;

        $this->lastQuery = $query;

        $args = func_get_args();
        $PDOS = call_user_func_array([&$this->PDO, 'prepare'], $args);

        $statement = new PDOStatement($this, $PDOS);

        $this->lastStatement = $statement;

        return $statement;
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

    public function errorInfo() {
        $error = $this->PDO->errorInfo();

        if($error[0] === 0) {
            $error = $this->lastStatement->errorInfo();
        }

        return $error;
    }
}
