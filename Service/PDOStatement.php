<?php declare(strict_types=1);
namespace PrimPack\Service;

class PDOStatement {
    protected \PDOStatement $PDOS;
    protected PDO $PDOp;

    public function __construct(PDO $PDOp, \PDOStatement $PDOS) {
        $this->PDOp = $PDOp;
        $this->PDOS = $PDOS;
    }

    public function bindColumn(string|int $column, mixed &$param, int $type = \PDO::PARAM_STR): void
    {
        if ($type === \PDO::PARAM_STR)
            $this->PDOS->bindColumn($column, $param);
        else
            $this->PDOS->bindColumn($column, $param, $type);
    }

    public function bindParam(string|int $column, mixed &$param, int $type): void
    {
        if ($type === 0)
            $this->PDOS->bindParam($column, $param);
        else
            $this->PDOS->bindParam($column, $param, $type);
    }

    public function execute(): mixed
    {
        $this->PDOp->numExecutes++;
        $args = func_get_args();

        $this->PDOp->queries[array_key_last($this->PDOp->queries)][2][] = $args;
        foreach ($args[0] as $index => $arg) {
            $type = htmlentities(var_export($arg, true));
            $this->PDOp->queries[array_key_last($this->PDOp->queries)][1] = str_replace("$index", "<b title='$type'>$index</b>", $this->PDOp->queries[array_key_last($this->PDOp->queries)][1]);
        }

        return call_user_func_array([&$this->PDOS, 'execute'], $args);
    }

    public function rowCount(): mixed
    {
        return $this->PDOS->rowCount();
    }

    public function fetch(int $fetch_style = \PDO::FETCH_OBJ, int $cursor_orientation = \PDO::FETCH_ORI_NEXT, int $cursor_offset = 0): mixed
    {
        return $this->PDOS->fetch($fetch_style, $cursor_orientation, $cursor_offset);
    }

    public function fetchAll(int $fetch_style = \PDO::FETCH_OBJ, ...$ctor_args): mixed
    {
        return $this->PDOS->fetchAll($fetch_style, ...$ctor_args);
    }

    public function fetchColumn(int $column_number = 0): mixed
    {
        return $this->PDOS->fetchColumn($column_number);
    }

    public function fetchObject(string $class): mixed
    {
        return $this->PDOS->fetchObject($class);
    }

    public function setFetchMode(int $mode, string $class): bool {
        return $this->PDOS->setFetchMode($mode, $class);
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
