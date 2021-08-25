<?php

namespace Framework\db;

use Framework\helpers\ArrayHelper;
use Framework\helpers\StringHelper;
use Framework\Application;
use Modules\DD;
use PDO;
use PDOStatement;

/**
 * Class Query
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package Framework\db
 */
class Query
{
    public const RESPONSE_GET_LAST_ID = 'id';
    public const RESPONSE_GET_AFFECTED_ROWS = 'affected_rows';
    public const RESPONSE_DEFAULT_MODE = self::RESPONSE_GET_LAST_ID;
    public const MAX_QUERY_LIMIT = 1000;
    public const EXCEPT_COLUMNS_ARR = ['id'];

    public static $RESPONSE_MODE = self::RESPONSE_DEFAULT_MODE;
    public static $MODE = PDO::FETCH_OBJ;
    protected static $removeWildcards = true;
    public static $CLASS;

    protected Database $db;
    protected PDO $pdo; // 0000 success - no errors
// DEBUG
//DD::dl($db->pdo->errorInfo());
//DD::dl($db->pdo->errorCode());
    public $stmt;

    protected $response = null;

    // QUERY
    protected string $query;
    protected string $select;
    protected string $from;
    protected string $join;
    protected string $where;
    protected string $groupBy;
    protected string $orderBy;
    protected string $limit;
    protected array $keyValueArray;
    protected array $valuesArray;
    protected array $attribsArray;

    protected static ?Query $inst = null;

    public function __construct()
    {
        $this->clearQueryParams();
        $this->db = Application::$app->db;
        $this->pdo = $this->db->pdo;
        $this->stmt = new PDOStatement();
    }

    public static function getInst(): Query
    {
        if (is_null(self::$inst)) {
            return new static();
        }

        return self::$inst;
    }

    // BATCH insert EXAMPLE
//        $migrations = [[1, 2, 'name1'], [5, 3, 'name2'], [5, 3, 3], [3, 3, 'name3']];
//        $res = (new Query())->insert('invoice_list', ['cust_id', 'order_id', 'filename'], $migrations);
    /**
     * @param string $tableName
     * @param array $attrArr
     * @param array $insertValues
     * @return bool|int int - inserted count; bool - whether record(s) inserted or not
     */
    public function insert(string $tableName, array $attrArr, array $insertValues)
    {
        if (empty(self::$RESPONSE_MODE)) {
            self::$RESPONSE_MODE = self::RESPONSE_DEFAULT_MODE;
        }

        if (empty($attrArr) || empty($insertValues)) {
            return false;
        }
        // reset keys e.g. 2 => 'value' to start the array from 0
        $insertValues = array_values($insertValues);
        $attrArr = array_values($attrArr);

        $this->assignKeyValuesArr($attrArr, $insertValues);
        if (ArrayHelper::isAssoc($this->keyValueArray)) {
            $this->nameInsert($tableName);
        } else {
            $this->questionMarkInsert($tableName);
        }
//        DD::dl($this->keyValueArray);

        return $this->executeQuery();
    }

    /**
     * @param string $tableName
     */
    public function nameInsert(string $tableName): void
    {
        $params = $this->prepareColumnValues();
        $this->query = $this->prepareInsertString(
            $tableName,
            implode(", ", $this->attribsArray),
            $params
        );
    }

    /**
     * @param string $tableName
     */
    public function questionMarkInsert(string $tableName): void
    {
        $oneRecordValues = '(' . $this->placeholders('?', sizeof($this->attribsArray)) . ')';
        // TODO not sure if it is the right way
        $multiInsert = $this->isMulti();
        $n = $multiInsert ? count($this->valuesArray) : 1;
//        dd($multiInsert);
        $arrayOfNValues = array_fill(
            0,
            $n,
            $oneRecordValues
        );
        $questionMarksStr = implode(',', $arrayOfNValues);

        $this->query = $this->prepareInsertString(
            $tableName,
            implode(", ", $this->attribsArray),
            $questionMarksStr
        );
    }


    // BATCH insert EXAMPLE
    //        $migrations = [[1, 2, 'name1'], [5, 3, 'name2'], [5, 3, 'name2.1'], [3, 3, 'name3']];
    //        $res = (new Query())->batchInsert('invoice_list', ['cust_id', 'order_id', 'filename'], $migrations);

//    public function batchInsert(string $tableName, array $fieldsArr, array $insertValues)
//    {
//        if (empty(self::$MODE)) {
//            self::$MODE = self::DEFAULT_MODE;
//        }
//
//        if (empty($fieldsArr) || empty($insertValues)) {
//            return false;
//        }
//
//        $insertValues = [];
//        $questionMarksArr = [];
//        foreach ($insertValues as $d) {
//            if (!is_array($d)) {
//                throw new Exception('Batch Insert Only!');
//            }
//
//            // [[0]=> "(?,?,?)" [1] => "(?,?,?)"]
//            $questionMarksArr[] = '(' . $this->placeholders('?', sizeof($d)) . ')';
//            // [0 => 'value1', 1 => 'value2', 2 => 'value3'...];
//            $insertValues = array_merge($insertValues, array_values($d));
//        }
//
//        if (empty($insertValues)) {
//            throw new Exception('You need to specify correct params!');
//        }
//
//        $sql = $this->prepareInsertString(
//            $tableName,
//            implode(",", $fieldsArr),
//            implode(',', $questionMarksArr)
//        );
//
//        $this->stmt = $this->pdo->prepare($sql);
//        return $this->executeQuery($insertValues);
//    }

    public function placeholders(string $text, int $count = 0, string $separator = ","): string
    {
        if ($count <= 0) {
            return '';
        }

        $result = array_fill(0, $count, $text);

        return implode($separator, $result);
    }

    public function executeQuery($values = []): bool
    {
//        $this->getMode();
        if (strlen($this->query) <= 0) {
            $this->mergeQuery();
        }

        if (empty($this->attribsArray)) {
            throw new \PDOException('attrArr is empty!');
        }

//        $this->debugWhere("INSERT");
        $this->valuesArray = empty($values) ? $this->valuesArray : $values;
        if (ArrayHelper::isAssoc($values)) {
            $this->keyValueArray = $values;
        }

//        $this->debugWhere('INSERT');
//        $this->debugWhere('INSERT INTO migrations');
        try {
            $this->stmt = $this->pdo->prepare($this->query);
            if (ArrayHelper::isAssoc($this->keyValueArray)) {
                foreach ($this->attribsArray as $attr) {
                    $this->stmt->bindValue(":$attr", $this->keyValueArray[$attr]);
                }
            }

            if (empty($this->valuesArray)) {
                return $this->stmt->execute();
            } elseif ($this->stmt->execute($this->valuesArray)) {
                return true;
            }
            throw new \PDOException('Statement can not be executed!');
        } catch (\PDOException $exception) {
            echo $this->query;
            throw new \InvalidArgumentException($exception->getMessage() . '; Query is not executed');
        }
    }

    /**
     * @description only for question mark insert
     * @param string $tableName
     * @param string $fields
     * @param string $questionMarks
     * @return string
     */
    public function prepareInsertString(string $tableName, string $fields, string $questionMarks): string
    {
        // VALUES %s is used for batch insert as (%s) will allow only 1
        return sprintf("INSERT INTO %s (%s) VALUES %s", $tableName, $fields, $questionMarks);
    }

    /**
     * @param array $attrArr
     * @return string
     */
    public function prepareColumnValues(array $attrArr = []): string
    {
        // TODO check if $attrArr must be first
        $this->attribsArray = empty($this->attribsArray) ? $attrArr : $this->attribsArray;
        return '(:' . implode(', :', $this->attribsArray) . ')';
    }

    public function select($select = '*'): self
    {
        if (is_array($select)) {
            $select = implode(',', $select);
        }
        if (!is_string($select)) {
            throw new \InvalidArgumentException('Wrong argument is supplied to select() method');
        }
        $this->select = 'SELECT ' . $select;

        return $this;
    }

    public function from($tables): self
    {
        $this->from = ' FROM ' . $this->separateWithCommas($tables);
        return $this;
    }

    /**
     * @return string
     */
    public function joinWhere(array $attribs): string
    {
        $whereAttribsArr = array_map(fn($attr) => "$attr = :$attr", $attribs);
        return implode(' AND ', $whereAttribsArr);
    }

    public function where($where): self
    {
        $this->assignKeyValuesArr($where);

        $condition = $this->joinWhere($this->attribsArray);

        $this->where = ' WHERE ' . $condition;
        return $this;
    }

    protected function nWhere($joinWord, $where): void
    {
        $this->mergeKeyValueArr($where);
        $condition = $this->joinWhere(array_keys($where));

        $this->where .= sprintf("$joinWord %s ", $condition);
    }

    protected function whereWithQuestionMarks(array $where, string $join = 'AND')
    {
        $this->mergeKeyValueArr($where);
        $condition = $this->getQuestionMarkQuery($where, true, $join);

        $this->where .= sprintf("WHERE %s ", $condition);
    }

    public function andWhere($where): self
    {
        $this->nWhere('AND', $where);

        return $this;
    }

    public function orWhere($where): self
    {
        $this->nWhere('OR', $where);

        return $this;
    }

    /** @param string|array $values
     * @return string
     */
    protected function separateWithCommas($values): string
    {
        if (is_array($values)) {
            return implode(', ', $values);
        }

        return $values;
    }

    /** @description removes or escapes array of symbols [*, _, ...]
     * @param array $arr
     * @return array
     */
    public static function handleWildcards(array $arr): array
    {
        if (empty($arr)) {
            return [];
        }

        if (self::$removeWildcards) {
            foreach ($arr as $key => $value) {
                $arr[$key] = StringHelper::removeWildCards($value);
            }
        } else {
            foreach ($arr as $key => $value) {
                $arr[$key] = StringHelper::escapeWildcards($value);
            }
        }
        return $arr;
    }

    public function one()
    {
        $this->limit = ' LIMIT 1';
//        DD::dd($this->getQuery());
        $this->executeQuery();

        if (isset($this->stmt) && $this->stmt->rowCount() == 1) {
            if (self::$CLASS) {
                $res = $this->stmt->fetchObject(self::$CLASS);
            } elseif (self::$MODE === PDO::FETCH_OBJ) {
                $res = $this->stmt->fetchObject();
            } else {
                $res = $this->stmt->fetch();
            }
            $this->clearQueryParams();
            return $res;
        }
        return false;
    }

    public function all()
    {
        $this->limit = ' LIMIT ' . self::MAX_QUERY_LIMIT;
//        DD::dd($this->getQuery());
        $this->executeQuery();

        if (isset($this->stmt) && $this->stmt->rowCount() > 0) {
            $res = $this->stmt->fetchAll(self::$MODE);
            $this->clearQueryParams();
            return $res;
        }
        return false;
    }

    protected function mergeQuery()
    {
        $this->query = $this->select .
            $this->from .
            $this->join .
            $this->where .
            $this->groupBy .
            $this->orderBy .
            $this->limit;
    }

    public function truncate(string $tableName): bool
    {
        $defConn = Application::$config['default'];
        $sql = sprintf("TRUNCATE `%s`.`%s`;", Application::$config['connections'][$defConn]['database'], $tableName);
        $this->stmt = $this->pdo->prepare($sql);
        return $this->stmt->execute();
    }

    public function dropTable($tableName)
    {
        // DROP TABLE IF EXISTS `rz_framework`.`migrations`
        $sql = $this->dbTable("DROP TABLE IF EXISTS", $tableName);
//        $sql = "SET FOREIGN_KEY_CHECKS = 0; $sql SET FOREIGN_KEY_CHECKS = 1;";
        $this->stmt = $this->pdo->prepare($sql);
        return $this->stmt->execute();
    }

    public function dropTables(array $tables): bool
    {
        try {
            if (sizeof($tables) <= 0) {
                throw new \InvalidArgumentException('EXCEPTION: Empty array of tables is supplied!');
            }
        } catch (\Exception $exception) {
            exit($exception->getMessage());
        }
        $sql = '';
//        foreach ($tables as $tableName) {
//            $sql .= $this->dbTable("DROP TABLE IF EXISTS", $tableName);
//        }
        foreach ($tables as $tableName) {
            $sql .= $this->dbTable('DROP TABLE IF EXISTS ', $tableName);
        }
//        dd($sql);
        return $this->pdo->exec($sql) !== false;
    }

    /**
     * @return string
     */
    protected function getResponse(): string
    {
        switch (self::$RESPONSE_MODE) {
            case self::RESPONSE_GET_LAST_ID:
                $this->response = $this->pdo->lastInsertId();
                break;
            case self::RESPONSE_GET_AFFECTED_ROWS:
                $this->response = $this->stmt->rowCount();
        }
        $this->stmt = null;
        return $this->response;
    }

    /**
     * @param string|array $limitParam
     * @return $this
     */
    public function limit($limitParam): self
    {
        $this->limit = ' LIMIT ' . $this->prepareLimit($limitParam);
        return $this;
    }

    /* HELPER METHODS DOWN BELOW */

    public function clearQueryParams()
    {
        $this->query = '';
        $this->select = '';
        $this->from = '';
        $this->join = '';
        $this->where = '';
        $this->groupBy = '';
        $this->orderBy = '';
        $this->limit = '';
        $this->keyValueArray = [];
        $this->attribsArray = [];
        $this->stmt = null; // close connection to avoid error
        self::$CLASS = null;
    }

    private function getQuery(): string
    {
        $this->mergeQuery();
        return $this->query;
    }

    /**
     * @param array|string $limitParam
     * @return array|string
     */
    private function prepareLimit($limitParam)
    {
        if (is_array($limitParam)) {
            return implode(',', $limitParam);
        } else {
            return $limitParam;
        }
    }

    public function setAttributes($arr)
    {
        $this->attribsArray = $arr;
    }

    private function assignKeyValuesArr($mixed, $values = []): void
    {
        // for where
        if (empty($values)) {
            $this->setArrays($mixed);
        } else {
            // TODO to remove
//            if (sizeof($mixed) !== sizeof($values)) {
//                $keys = array_fill(0, sizeof($values), $mixed[0]);
//                $this->keyValueArray = array_combine($keys, $values);
//            } else {
//                dd(1);
//                $this->keyValueArray = array_combine($mixed, $values);
//            }
            $this->attribsArray = $mixed;
            $this->valuesArray = $values;
        }
    }

    private function mergeKeyValueArr($keyValueArr)
    {
        $this->keyValueArray = array_merge($this->keyValueArray, $keyValueArr);
        $this->attribsArray = array_merge($this->attribsArray, array_keys($keyValueArr));
        $this->valuesArray = array_merge($this->valuesArray, array_values($keyValueArr));
    }

    private function debugWhere(string $string)
    {
        if (strpos($this->query, $string) !== false) {
            DD::dl($this->query);
            echo 'values Arr';
            DD::dl($this->valuesArray);
            echo 'Attribs Arr';
            DD::dl($this->attribsArray);
            DD::dl($this->keyValueArray);
            DD::dd('exit');
        }
    }

    /**
     * @param string $table DB table_name
     * @param array $data data to insert to $table
     * @param string $where condition to update
     */
    public function update(string $table, array $data, array $where)
    {
//        ksort($data);
        $this->setArrays($data);
        $fields = $this->getQuestionMarkQuery($this->attribsArray, false);
        $this->whereWithQuestionMarks($where);
        $this->limit(1);

        $this->query = "UPDATE $table SET $fields " . $this->where . $this->limit;
//        dd($this->query);
//        dd($this->keyValueArray);
        $this->executeQuery();
        return $this->stmt->rowCount();
    }


    /**
     * @return int affected rows count
     */
    public function delete(string $table, int $id, int $limit = 1): int
    {
        return $this->deleteWhere($table, 'id', $id, $limit);
    }

    /**
     * @return int affected rows count
     */
    public function deleteWhere(string $table, string $fieldName, string $value, int $limit = 100): int
    {
        $this->stmt = null;
        $this->query = "DELETE FROM $table WHERE $fieldName = $value LIMIT $limit";
        $this->executeQuery();

        return $this->stmt->rowCount();
    }

    /**
     * @return int affected rows count
     */
    public function deleteWhereIn(string $table, string $fieldName, array $values, $limit = 100): int
    {
        $this->stmt = null;
        $in = $this->placeholders('?', sizeof($values));
        $this->query = "DELETE FROM $table WHERE $fieldName IN($in) LIMIT $limit";
        $this->attribsArray[] = $fieldName;
        $this->valuesArray = $values;
        $this->executeQuery();

        return $this->stmt->rowCount();
    }

    private function dbTable(string $command, string $tableName): string
    {
        $defConn = Application::$config['default'];
        return sprintf(
            "%s `%s`.`%s`;",
            $command,
            Application::$config['connections'][$defConn]['database'],
            $tableName
        );
    }

    private function setArrays($mixed)
    {
        $this->keyValueArray = $mixed;
        $this->attribsArray = array_keys($mixed);
        $this->valuesArray = array_values($mixed);
    }

    protected function getQuestionMarkQuery(array $arr, $useKey = true, $join = ', ')
    {
        $fields = '';
        $join = strpos($join, ',') && strlen($join) <= 2 ? $join : " $join ";
        foreach ($arr as $key => $value) {
            $fields .= $useKey ? "$key=?$join" : "$value=?$join";
        }
        return substr($fields, 0, -(strlen($join)));
    }

    protected function isMulti(): bool
    {
        return count($this->valuesArray) / count($this->attribsArray) > 1;
    }
}
