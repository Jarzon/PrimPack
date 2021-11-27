<?php declare(strict_types=1);
namespace PrimPack\Service;

class PDOStatement implements \IteratorAggregate {
    protected \Traversable $PDOS;
    protected PDO $PDOp;

    public function __construct(PDO $PDOp, \Traversable $PDOS) {
        $this->PDOp = $PDOp;
        $this->PDOS = $PDOS;
    }

    public function __call($func, array $args): mixed
    {
        return call_user_func_array([&$this->PDOS, $func], $args);
    }

    public function bindColumn($column, &$param, $type = ''): void
    {
        $this->PDOp->lastParams = [$column, $param, $type];

        if ($type === '')
            $this->PDOS->bindColumn($column, $param);
        else
            $this->PDOS->bindColumn($column, $param, $type);
    }

    public function bindParam($column, &$param, $type = ''): void
    {
        $this->PDOp->lastParams = [$column, $param, $type];

        if ($type === '')
            $this->PDOS->bindParam($column, $param);
        else
            $this->PDOS->bindParam($column, $param, $type);
    }

    public function execute(): mixed
    {
        $this->PDOp->numExecutes++;
        $args = func_get_args();

        $this->PDOp->lastParams = empty($args)? []: $args;

        return call_user_func_array([&$this->PDOS, 'execute'], $args);
    }

    public function rowCount(): mixed
    {
        return $this->PDOS->rowCount();
    }

    public function __get($property): mixed
    {
        return $this->PDOS->$property;
    }

    public function getIterator(): \Traversable
    {
        return $this->PDOS;
    }

    public function errorInfo(): mixed
    {
        return $this->PDOS->errorInfo();
    }
}
