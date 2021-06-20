<?php

namespace app\core\db;

use app\common\helpers\ArrayHelper;
use app\common\helpers\StringHelper;
use app\core\Application;
use Exception;
use modules\DD\DD;
use PDO;
use PDOStatement;

/**
 * Class Query
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package app\core\db
 */
class Query
{
    public const RESPONSE_GET_LAST_ID = 'id';
    public const RESPONSE_GET_AFFECTED_ROWS = 'affected_rows';
    public const RESPONSE_DEFAULT_MODE = self::RESPONSE_GET_LAST_ID;
    public const MAX_QUERY_LIMIT = 1000;

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

    public function __construct()
    {
        $this->clearQueryParams();
        $this->db = Application::$app->db;
        $this->pdo = $this->db->pdo;
        $this->stmt = new PDOStatement();
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
        $arrayOfNValues = array_fill(
            0,
            count($this->keyValueArray),
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

        $values = empty($values) ? $this->keyValueArray : [];

        try {
            $this->stmt = $this->pdo->prepare($this->query);

            if (ArrayHelper::isAssoc($values)) {
                foreach ($this->attribsArray as $attr) {
                    $this->stmt->bindValue(":$attr", $values[$attr]);
                }
            }

            if (!$this->stmt->execute($values)) {
                throw new \PDOException('Statement can not be executed!');
            }
            return true;
        } catch (\PDOException $exception) {
            DD::dl($this->getQuery());
            echo "error \r\n" . PHP_EOL;
            exit($exception->getMessage());
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


    public function where($where): self
    {
        $this->assignKeyValuesArr($where);

        $condition = $this->joinWhere($this->attribsArray);

        $this->where = ' WHERE ' . $condition;
        return $this;
    }


    public function andWhere($where): self
    {
        $this->mergeKeyValueArr($where);
        $condition = $this->joinWhere(array_keys($where));

        $this->where .= ' AND ' . $condition;
//        DD::dd($this->where);
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

    /** @description removes or escapes array of symbols [*, _, ...] */
    public static function handleWildcards($arr)
    {
        if (empty($arr)) {
            return false;
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

    /**
     * @return string
     */
    public function joinWhere(array $attribs): string
    {
        $whereAttribsArr = array_map(fn($attr) => "$attr = :$attr", $attribs);
        return implode(' AND ', $whereAttribsArr);
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
     * @param $limitParam
     * @return array|string
     */
    private function prepareNumberParams($limitParam)
    {
        if (is_array($limitParam)) {
            return implode(',', $limitParam);
        } else {
            return explode(', ', $limitParam);
        }
    }

    /**
     * @param string|array $limitParam
     * @return $this
     */
    public function limit($limitParam): self
    {
        $this->limit = ' LIMIT ' . $this->prepareNumberParams($limitParam);
        return $this;
    }

    public function setAttributes($arr)
    {
        $this->attribsArray = $arr;
    }

    private function assignKeyValuesArr($mixed, $values = []): void
    {
        if (empty($values)) {
            $this->keyValueArray = $mixed;
            $this->attribsArray = array_keys($mixed);
            $this->valuesArray = array_values($mixed);
        } else {
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
}
