<?php declare(strict_types=1);
namespace PrimPack\Service;

class PDO {
    protected \PDO $PDO;
    public int $numExecutes = 0;
    public int $numStatements = 0;
    public array $queries = [];
    public PDOStatement $lastStatement;

    const FETCH_GROUP = \PDO::FETCH_GROUP;
    const FETCH_UNIQUE = \PDO::FETCH_UNIQUE;
    const FETCH_OBJ = \PDO::FETCH_OBJ;
    const FETCH_COLUMN = \PDO::FETCH_COLUMN;
    const FETCH_CLASS = \PDO::FETCH_CLASS;
    const FETCH_INTO = \PDO::FETCH_INTO;
    const FETCH_FUNC = \PDO::FETCH_FUNC;
    const FETCH_LAZY = \PDO::FETCH_LAZY;
    const FETCH_KEY_PAIR = \PDO::FETCH_KEY_PAIR;
    const FETCH_NAMED = \PDO::FETCH_NAMED;
    const FETCH_NUM = \PDO::FETCH_NUM;
    const FETCH_BOUND = \PDO::FETCH_BOUND;
    const FETCH_CLASSTYPE = \PDO::FETCH_CLASSTYPE;
    const FETCH_SERIALIZE = \PDO::FETCH_SERIALIZE;
    const FETCH_PROPS_LATE = \PDO::FETCH_PROPS_LATE;

    public function __construct(string $dsn, ?string $user = '', ?string $pass = '', ?array $driver_options = []) {
        $driver_options += [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ];

        $this->PDO = new \PDO($dsn, $user, $pass, $driver_options);
    }
    public function __call($func, $args) {
        return call_user_func_array([&$this->PDO, $func], $args);
    }

    public function prepare($query, $options = NULL): PDOStatement|false
    {
        $this->numStatements++;

        $args = func_get_args();
        $stackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $file = '';
        foreach ($stackTrace as $st) {
            if(str_contains($st['class'], 'Model')) {
                $file = $st['class'] . "::" . $st['function'];
                break;
            }
        }
        $this->queries[] = [$file, $query];
        $PDOS = call_user_func_array([&$this->PDO, 'prepare'], $args);

        $statement = new PDOStatement($this, $PDOS);

        $this->lastStatement = $statement;

        return $statement;
    }

    public function query(string $query, ?int $fetch_mode = null, mixed ...$fetch_mode_args): PDOStatement|false
    {
        $this->numExecutes++;
        $this->numStatements++;

        $args = func_get_args();

        $stackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $file = '';
        foreach ($stackTrace as $st) {
            if(str_contains($st['class'], 'Model')) {
                $file = $st['class'] . "::" . $st['function'];
                break;
            }
        }
        $this->queries[] = [$file, $query];
        $PDOS = call_user_func_array([&$this->PDO, 'query'], $args);

        return new PDOStatement($this, $PDOS);
    }

    public function exec($query): int|false
    {
        $this->numExecutes++;
        $this->numStatements++;

        $args = func_get_args();

        return call_user_func_array([&$this->PDO, 'exec'], $args);
    }

    public function lastInsertId($name = null): string|false
    {
        return $this->PDO->lastInsertId($name);
    }

    public function getAttribute($attribute): mixed
    {
        return $this->PDO->getAttribute($attribute);
    }

    public function errorInfo(): array
    {
        $error = $this->PDO->errorInfo();

        if($error[0] === 0) {
            $error = $this->lastStatement->errorInfo();
        }

        return $error;
    }
}
