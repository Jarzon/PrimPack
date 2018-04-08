<?php
namespace PrimPack\Service;

class PDOStatement implements \IteratorAggregate {
    protected $PDOS;
    protected $PDOp;

    public function __construct($PDOp, object $PDOS) {
        $this->PDOp = $PDOp;
        $this->PDOS = $PDOS;
    }

    public function __call($func, $args) {
        return call_user_func_array([&$this->PDOS, $func], $args);
    }

    public function bindColumn($column, &$param, $type = '') {
        $this->PDOp->lastParams = [$column, $param, $type];

        if ($type === '')
            $this->PDOS->bindColumn($column, $param);
        else
            $this->PDOS->bindColumn($column, $param, $type);
    }

    public function bindParam($column, &$param, $type = '') {
        $this->PDOp->lastParams = [$column, $param, $type];

        if ($type === '')
            $this->PDOS->bindParam($column, $param);
        else
            $this->PDOS->bindParam($column, $param, $type);
    }

    public function execute() {
        $this->PDOp->numExecutes++;
        $args = func_get_args();

        $this->PDOp->lastParams = empty($args)? '': $args[0];

        return call_user_func_array([&$this->PDOS, 'execute'], $args);
    }

    public function rowCount()
    {
        return $this->PDOS->rowCount();
    }

    public function __get($property) {
        return $this->PDOS->$property;
    }

    public function getIterator() {
        return $this->PDOS;
    }
}