<?php

namespace Framework\db\QueryBuilder;

use Framework\db\exception\QueryException;
use Modules\DD;

abstract class QueryBuilder
{
    public const TYPE_SELECT = 'select';

    protected ?string $type = '';
    protected ?string $columns = '';
    protected ?string $table = '';
    protected ?string $orderBy = '';
    protected ?int $limit = null;
    protected ?int $offset = null;

    /**
     * Fetch all rows matching the current query
     */
    public function all(): array
    {
        if (!isset($this->type)) {
            $this->select();
        }

        $statement = $this->prepare();
//        $statement->execute($this->getWhereValues());
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Prepare a query against a particular connection
     */
    public function prepare(): \PDOStatement
    {
        $query = '';

        if ($this->type === self::TYPE_SELECT) {
            $query = $this->compileSelect($query);
            $query = $this->compileLimit($query);
        }

        if (empty($query)) {
            throw new QueryException('Unrecognised query type');
        }

        return $this->connection->pdo()->prepare($query);
    }

    /**
     * Add select clause to the query
     */
    protected function compileSelect(string $query): string
    {
        $query .= " SELECT {$this->columns} FROM {$this->table}";

        return $query;
    }

    /**
     * Add limit and offset clauses to the query
     */
    protected function compileLimit(string $query): string
    {
        if ($this->limit) {
            $query .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $query .= " OFFSET {$this->offset}";
        }

        return $query;
    }

    /**
     * Add order by claus to the query
     */
    protected function compileOrderBy(string $query): string
    {
        if ($this->orderBy) {
            $query .= " ORDER BY {$this->orderBy}";
        }

        return $query;
    }

    /**
     * Fetch the first row matching the current query
     */
    public function first(): array
    {
        $statement = $this->take(1)->prepare();
        $statement->execute();

        // TODO check if fetch can be used
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Fetch the first row matching the current query
     */
    public function last(): array
    {
        $statement = $this->take(1)->orderBy('id DESC')->prepare();
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Limit a set of query results so that it's possible
     * to fetch a single or limited batch of rows
     */
    public function take(int $limit, int $offset = 0): self
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * Indicate which table the query is targeting
     */
    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Indicate the query type is a "select" and remember
     * which fields should be returned by the query
     */
    public function select(string $columns = '*'): self
    {
        $this->type = self::TYPE_SELECT;
        $this->columns = $columns;

        return $this;
    }

    public function orderBy($by = 'id DESC'): self
    {
        $this->orderBy = $by;
        return $this;
    }
}
