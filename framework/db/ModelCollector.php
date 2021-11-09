<?php

namespace Framework\db;

use Framework\db\QueryBuilder\QueryBuilder;
use Modules\DD;

class ModelCollector
{
    private QueryBuilder $builder;
    private string $class;

    public function __construct(QueryBuilder $builder, string $class)
    {
        $this->builder = $builder;
        $this->class = $class;
    }

    public function __call(string $method, array $parameters = [])
    {
        $result = $this->builder->$method(...$parameters);

        // in case it's a fluent method...
        if ($result instanceof QueryBuilder) {
            $this->builder = $result;
            return $this;
        }

        return $result;
    }

    public function first()
    {
        /** @var BaseActiveRecord $class */
        $class = $this->class;

        $row = $this->builder->first();

        if (!is_null($row)) {
                $row = $class::with($row);
        }

        return $row;
    }

    public function all()
    {
        /** @var BaseActiveRecord $class */
        $class = $this->class;

        $rows = $this->builder->all();

        foreach ($rows as $i => $row) {
            $rows[$i] = $class::with($row);
        }

        return $rows;
    }
}
