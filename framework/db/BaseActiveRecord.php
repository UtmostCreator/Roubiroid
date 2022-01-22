<?php

namespace Framework\db;

use Framework\db\Connection\Connection;
use Framework\db\Connection\MysqlConnection;
use Framework\db\Connection\SqliteConnection;
use Framework\helpers\Config;
use Modules\DD;

abstract class BaseActiveRecord
{
    protected Connection $connection;
    protected string $table;
    /** @var array $attributes an array of all DB columns,
     * that implements ORM;
     * to retrieve data the MAGIC __get method is used;
     * to set the data the MAGIC __set method is used.
     */
    protected array $attributes = [];
    /** @description  is used to show the attributes/fields that were changed to update/save only them */
    protected array $dirty = [];

    public function __get(string $property)
    {
        $getter = 'get' . ucfirst($property) . 'Attribute';

        $value = null;

        if (method_exists($this, $property)) {
            /** @var Relationship $relationship */
            $relationship = $this->$property();
            $method = $relationship->method;
            $value = $relationship->$method();
        }

        if (method_exists($this, $getter)) {
            $value = $this->$getter($this->attributes[$property] ?? null);
        }

        if (isset($this->attributes[$property])) {
            $value = $this->attributes[$property];
        }

        if (isset($this->casts[$property]) && is_callable($this->casts[$property])) {
            $value = $this->casts[$property]($value);
        }

        return $value;
    }

    // TODO check if a $property check required in case it is empty string ''
    public function __set(string $property, $value)
    {
        $setter = 'set' . ucfirst($property) . 'Attribute';

        if (!in_array($property, $this->dirty)) {
            array_push($this->dirty, $property);
        }

        if (method_exists($this, $setter)) {
            $this->attributes[$property] = $this->$setter($value);
            return;
        }

        $this->attributes[$property] = $value;
    }

    public function setConnection(Connection $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    public function getConnection(): Connection
    {
        if (!isset($this->connection)) {
            $this->connection = app('database');
        }

        return $this->connection;
    }

//    public function getConnection(): Connection
//    {
//        if (!isset($this->connection)) {
//            $factory = new Factory();
//            $factory->addConnector('mysql', function ($config) {
//                return new MysqlConnection($config);
//            });
//            $factory->addConnector('sqlite', function ($config) {
//                return new SqliteConnection($config);
//            });
//
//            $this->connection = $factory->connect(Config::get('connections=default'));
//        }
//
//        return $this->connection;
//    }

    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function getTable(): string
    {
        if (!isset($this->table)) {
            /** TODO only php 8+
             * $reflector = new \ReflectionClass(static::class);
             *
             * foreach ($reflector->getAttributes() as $attribute) {
             * if ($attribute->getName() == TableName::class) {
             * return $attribute->getArguments()[0];
             * }
             * }
             */
            throw new \Exception('$table is not set and getTable is not defined');
        }

        return $this->table;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * creates a model and then
     * populates the array of attributes for specific model
     */
    public static function with(array $attributes = []): self
    {
        $model = new static();
        $model->attributes = $attributes;

        return $model;
    }

    /**
     * @throws \Exception
     */
    public static function query()
    {
        $model = new static();
        $queryBuilder = $model->getConnection()->query();

//        return $queryBuilder->from($model->getTable());
        return (new ModelCollector($queryBuilder, static::class))
            ->from($model->getTable());
    }

    /**
     * @throws \Exception
     */
    public static function __callStatic(string $method, array $parameters = [])
    {
        return static::query()->$method(...$parameters);
    }

    /**
     * @alias to update
     * @description this method designed either to update an existing data or insert a new one
     */
    public function save(): self
    {
        $values = [];

        foreach ($this->dirty as $dirtyProp) {
            $values[$dirtyProp] = $this->attributes[$dirtyProp];
        }

        $keys = $this->getFillable();
//        DD::dd($keys);
        $dbValues = array_map(fn ($el) => $this->attributes[$el], $keys);
        $data = [$keys, $dbValues];

        $query = static::query();
//        DD::dd($data);
        if (isset($this->attributes['id'])) {
            $query
                ->where('id', $this->attributes['id'])
                ->update(...$data);

            return $this;
        }

        $query->insert(...$data);

        $this->attributes['id'] = $query->getLastInsertId();
        $this->dirty = [];

        return $this;
    }

    /**
     * updates the record
     * refers to save that implements the logic
     */
    public function update(): self
    {
        $this->save();
        return $this;
    }

    // TODO delete and save method SHOULD they return $this? as then we could bug if you delete and then try to save it
    public function delete($columnName = 'id'): self
    {
        if (isset($this->attributes[$columnName])) {
            static::query()
                ->where($columnName, $this->attributes[$columnName])
                ->delete();
        }

        return $this;
    }

    public function getDirty()
    {
        return $this->dirty;
    }

    /** @description casts $value to Integer value */
    public function toInt($value): int
    {
        return (int)$value;
    }

    /** connects two related models and allows to access
     * from one model another model
     * there is a table user and profile.
     * user hasOne profile; SELECT * FROM profile WHERE user_id = $this->attributes[$primaryKey]
     * where $this->attributes[$foreignKey] is the `id` from user table
     */
    public function hasOne(string $class, string $foreignKey, string $primaryKey = 'id'): Relationship
    {
        /** @var BaseActiveRecord $model */
        /** @var BaseActiveRecord $class */
        $model = new $class();
        $query = $class::query()->from($model->getTable())->where($foreignKey, $this->attributes[$primaryKey]);

        return new Relationship($query, 'first');
//        return (new Relationship($query, 'first'))();
    }

    /** connection models in connection ONE to MANY
     * a user may have many orders
     * e.g. SELECT * FROM orders WHERE id = $this->attributes[$primaryKey]
     * where $this->attributes[$primaryKey] is the order's id
     */
    public function hasMany(string $class, string $foreignKey, string $primaryKey = 'id')
    {
        /** @var BaseActiveRecord $model */
        /** @var BaseActiveRecord $class */
        $model = new $class();
        $query = $class::query()->from($model->getTable())->where($foreignKey, $this->attributes[$primaryKey]);

        return new Relationship($query, 'all');
    }

    /** connects the secondary table with primary
     * where profile is 2ndary and user is primary
     * there is a table user and profile.
     * profile belongsTo user, that means user.id = profile.user_id
     * SELECT * FROM user WHERE id = $this->attributes[$foreignKey]
     * where $this->attributes[$foreignKey] is the user_id from profile table
     */
    public function belongsTo(string $class, string $foreignKey, string $primaryKey = 'id')
    {
        /** @var BaseActiveRecord $model */
        /** @var BaseActiveRecord $class */
        $model = new $class();
        $query = $class::query()->from($model->getTable())->where($primaryKey, $this->attributes[$foreignKey]);

        return new Relationship($query, 'first');
    }

    // TODO by IDs array
    public static function find(int $id): self
    {
        return static::where('id', $id)->first();
    }

    public function getDb()
    {
        return app()->getDb();
    }
}
