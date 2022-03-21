<?php

namespace Framework\db\QueryBuilder;

use Framework\db\Exception\QueryException;
use Framework\db\Query;
use Modules\DD;

abstract class QueryBuilder
{
    public const TYPE_SELECT = 'select';
    public const TYPE_INSERT = 'insert';
    public const TYPE_UPDATE = 'update';
    public const TYPE_DELETE = 'delete';

    protected ?int $limit = null;
    protected ?int $offset = null;
    protected ?string $type = null;
    protected ?string $table = '';
    protected ?string $orderBy = '';
    protected array $values = [];
    protected array $columns = [];
    protected array $wheres = [];

    /**
     * Prepare a query against a particular connection
     */
    public function prepare(): \PDOStatement
    {
        $query = '';

        if ($this->type === self::TYPE_SELECT) {
            $query = $this->compileSelect($query);
            $query = $this->compileWheres($query);
            if (empty($this->orderBy)) {
                $this->orderBy();
            }
            $query = $this->compileOrderBy($query);
            $query = $this->compileLimit($query);
        }

        if ($this->type === self::TYPE_INSERT) {
            $query = $this->compileInsert($query);
        }

        if ($this->type === self::TYPE_UPDATE) {
            $query = $this->compileUpdate($query);
            $query = $this->compileWheres($query);
        }

        if ($this->type === self::TYPE_DELETE) {
            $query = $this->compileDelete($query);
            $query = $this->compileWheres($query);
        }

        if (empty($query)) {
            throw new QueryException('Unrecognised query type');
        }

        // TODO add query logger class (to show later all requests)
        try {
            return $this->connection->pdo()->prepare($query);
        } catch (\Throwable $e) {
            throw new \PDOException(sprintf("ERROR: %s; in query: %s", $e->getMessage(), $query));
        }
    }

    public function executeQuery(string $query)
    {

        if (empty($query)) {
            throw new QueryException('Unrecognised query type');
        }

        // TODO add query logger class (to show later all requests)
        return $this->connection->pdo()->prepare($query);
    }

    /**
     * Indicate the query type is a "select" and remember
     * which fields should be returned by the query
     * @param string|array $columns
     * @return QueryBuilder
     */
    public function select($columns = '*'): self
    {
        if (is_string($columns)) {
//            $columns = [$columns];
            $columns = explode(', ', $columns);
        }

        $this->type = self::TYPE_SELECT;
        $this->columns = $columns;

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
     * Alias to from - can be used to specify table to insert to
     */
    public function to(string $table): self
    {
        $this->from($table);
        return $this;
    }

    /**
     * Store where clause data for later queries
     */
    public function where(string $column, $comparator, $value = null): self
    {
        if (is_null($value) && !is_null($comparator)) {
            $this->wheres[] = [$column, '=', $comparator];
        } else {
            $this->wheres[] = [$column, $comparator, $value];
        }

        return $this;
    }

    public function orderBy(string $by = 'id DESC'): self
    {
        $this->orderBy = $by;
        return $this;
    }

    /**
     * Limit a set of query results so that it's possible
     * to fetch a single or limited batch of rows
     * SELECT * FROM tbl LIMIT 5,10;  # Retrieve rows 6-15
     */
    public function take(int $limit, int $offset = 0): self
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * Fetch the first row matching the current query
     */
    public function first(): array
    {
        if (!isset($this->type)) {
            $this->select();
        }

        $statement = $this->take(1)->prepare();
        $statement->execute($this->getWhereValues());

        // TODO check if fetch can be used
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (count($result) === 1) {
            return $result[0];
        }

        return [];
    }

    /**
     * Fetch all rows matching the current query
     */
    public function all(): array
    {
        if (!isset($this->type)) {
            $this->select();
        }

        $statement = $this->prepare();
//        DD::dd($statement);
//        $statement->execute($this->getWhereValues());
        $statement->execute($this->getWhereValues());

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Fetch the first row matching the current query
     */
    public function last(): array
    {
        $statement = $this->take(1)->orderBy('id DESC')->prepare();
        $statement->execute();

        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Insert a row of data into the table specified in the query
     * and return the number of affected rows
     */
    // TODO add default validation
    public function insert(array $columns, array $values) // : int
    {
        $this->type = self::TYPE_INSERT;
        $this->columns = $columns;
        $this->values = $values;

        $statement = $this->prepare();
        $statement->execute($values);
        return $statement->rowCount();
    }

    // TODO add default validation
    public function update(array $columns, array $values)
    {
        $this->type = self::TYPE_UPDATE;
        $this->columns = $columns;
        $this->values = $values;

        $statement = $this->prepare();
        $statement->execute($this->getWhereValues() + $values);
    }

    /**
     * Delete a row from the database
     */
    public function delete(): int
    {
        $this->type = self::TYPE_DELETE;

        $statement = $this->prepare();

        return $statement->execute($this->getWhereValues());
    }

    protected function joinColumns(): string
    {
        return join(', ', $this->columns);
    }

    /**
     * Get the values for the where clause placeholders
     */
    protected function getWhereValues(): array
    {
        $values = [];

        if (count($this->wheres) === 0) {
            return $values;
        }

        foreach ($this->wheres as $where) {
            $values[$where[0]] = $where[2];
        }

        return $values;
    }

    /**
     * Get the ID of the last row that was inserted
     */
    public function getLastInsertId(): string
    {
        return $this->connection->pdo()->lastInsertId();
    }

    /**
     * Add select clause to the query
     */
    protected function compileSelect(string $query): string
    {
        $joinedColumns = $this->joinColumns();
        $query .= " SELECT {$joinedColumns} FROM {$this->table}";

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
     * Add where clauses to the query
     */
    protected function compileWheres(string $query): string
    {
        if (count($this->wheres) === 0) {
            return $query;
        }

        $query .= ' WHERE';

        foreach ($this->wheres as $i => $where) {
            if ($i > 0) {
                $query .= ', ';
            }

            [$column, $comparator, $value] = $where;

            $query .= " {$column} {$comparator} :$column";
        }

        return $query;
    }

    /**
     * Add insert clause to the query
     */
    protected function compileInsert(string $query): string
    {
        $joinedColumns = $this->joinColumns();
        $joinedPlaceholders = join(', ', array_map(fn($column) => ":{$column}", $this->columns));

        // e.g. " INSERT INTO tableName (name, description) VALUES (:name, :description)"
        $query .= " INSERT INTO {$this->table} ({$joinedColumns}) VALUES ({$joinedPlaceholders})";
        return $query;
    }

    /**
     * Add update clause to the query
     */
    protected function compileUpdate(string $query): string
    {
        $joinedColumns = implode(', ', array_map(fn($column) => "{$column} = :$column", $this->columns));
        $joinedColumns = rtrim(trim($joinedColumns), ',');
//        foreach ($this->columns as $i => $column) {
//            if ($i > 0) {
//                $joinedColumns .= ', ';
//            }
//
//            $joinedColumns .= "{$column} = :$column";
//        }

        $query .= " UPDATE {$this->table} SET {$joinedColumns}";
        return $query;
    }

    /**
     * Add delete clause to the query
     */
    protected function compileDelete(string $query): string
    {
        $query .= " DELETE FROM {$this->table}";
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

    public function isRecordExist(string $inTable, array $columns, array $values): bool
    {
        $this->type = self::TYPE_SELECT;
        $this->columns = $columns;

        $this->select('1');
        $this->from($inTable);
        $this->whereArr($columns, $values);
        $this->limit = 1;

        $statement = $this->prepare();

        $statement->execute($this->getWhereValues());

//        $result = $statement->fetch(\PDO::FETCH_ASSOC);
//        DD::dd($statement->fetchColumn());
        return $statement->fetchColumn();
    }


    public function hasColumn(string $table, string $name)
    {
        $query = sprintf("SHOW COLUMNS FROM `%s` WHERE Field = '%s'", $table, $name);
        $statement = $this->executeQuery($query);
        $statement->execute();
        return $statement->fetchColumn();
    }

    private function whereArr($columns, $values, $comparator = '=')
    {
        for ($i = 0; $i < count($columns); $i++) {
            $this->where($columns[$i], $comparator, $values[$i]);
        }
    }

}
