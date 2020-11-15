<?php

class PS_Collection implements IteratorAggregate, Countable
{
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    /**
     * @var CI_DB_query_builder
     */
    protected $_db;

    /**
     * @var string
     */
    protected $_mainTable;

    /**
     * @var string
     */
    protected $_tableAlias;

    /**
     * @var array
     */
    protected $_fieldsToSelect = [];

    /**
     * @var array
     */
    protected $_items = [];

    /**
     * @var bool
     */
    protected $_isLoad;

    /**
     * @var string
     */
    protected $_modelClass;

    /**
     * PS_Collection constructor.
     *
     * @param $db CI_DB_query_builder
     * @param $mainTable
     * @param string $modelClass
     * @param string $alias
     */
    public function __construct($db, $mainTable, $modelClass = PS_Object::class, $alias = 'main_table')
    {
        $this->_db = $db;
        $this->_tableAlias = $alias;
        $this->_modelClass = $modelClass;
        $this->_mainTable = $mainTable;
    }

    /**
     * Implementation of IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        $this->load();
        return new ArrayIterator($this->_items);
    }

    /**
     * Retrieve count of collection loaded items
     *
     * @return int
     */
    public function count()
    {
        $this->load();
        return count($this->_items);
    }

    /**
     * @param $field
     */
    public function addFieldToSelect($field)
    {
        $this->_fieldsToSelect[] = $this->_tableAlias . '.' . $field;
    }

    /**
     * @return $this
     */
    public function load()
    {
        if (is_null($this->_isLoad)) {
            $result = $this->_load();
            foreach ($result->result_array() as $item) {
                $this->_items[] = new $this->_modelClass($item);
            }

            $this->_isLoad = true;
        }
        return $this;
    }

    /**
     * Load collection data
     *
     * @return CI_DB_result
     */
    protected function _load()
    {
        if (!count($this->_fieldsToSelect)) {
            $this->_db->select('*');
        } else {
            $this->_db->select(implode(', ', $this->_fieldsToSelect));
        }
        $this->_db->from($this->_mainTable . ' as ' . $this->_tableAlias);

        $result = $this->_db->get();
        return $result;
    }

    /**
     * @return AbstractModel
     */
    public function getFirstItem()
    {
        try {
            $item = $this->_load()->first_row('array');
        } catch (Exception $e) {
            log_message('exception', $e->getMessage());
            $item = [];
        }

        return new $this->_modelClass($item);
    }

    /**
     * @return AbstractModel
     */
    public function getLastItem()
    {
        try {
            $item = $this->_load()->last_row('array');
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            $item = [];
        }
        return new $this->_modelClass($item);
    }

    /**
     * Retrieve items
     *
     * @return array
     */
    public function getItems()
    {
        $this->load();
        return $this->_items;
    }

    /**
     * Set order
     *
     * @param $field
     * @param string $direction
     * @return $this
     */
    public function addOrder($field, $direction = self::ORDER_ASC)
    {
        $this->_db->order_by($this->_tableAlias . '.' . $field, $direction);
        return $this;
    }

    /**
     * @param $fieldName
     * @param $condition
     * @return string
     */
    protected function _getConditionSql($fieldName, $condition)
    {
        if (is_array($fieldName)) {
            $orSql = array();
            foreach ($fieldName as $key => $name) {
                if (isset($condition[$key])) {
                    if (is_array($name)) {
                        foreach ($name as &$v) {
                            $v = $this->_tableAlias . '.' . $v;
                        }
                        $concatSql = 'CONCAT(' . implode(", ' ', ", $name) . ')';
                        $orSql[] = $this->_addConditionSql($concatSql, $condition[$key]);
                    } else {
                        $orSql[] = '(' . $this->_getConditionSql($name, $condition[$key]) . ')';
                    }
                }
            }
            $sql = '(' . join(' OR ', $orSql) . ')';
            return $sql;
        }
        $fieldName = $this->_tableAlias . '.' . $fieldName;
        $sql = $this->_addConditionSql($fieldName, $condition);
        return $sql;
    }

    protected function _addConditionSql($fieldName, $condition)
    {
        $sql = '';
        if (is_array($condition)) {
            if (isset($condition['from']) || isset($condition['to'])) {
                if (isset($condition['from'])) {
                    $sql .= "{$fieldName} >= {$this->_db->escape($condition['from'])}";
                }
                if (isset($condition['to'])) {
                    $sql .= empty($sql) ? '' : ' and ';

                    $sql .= "{$fieldName} >= {$this->_db->escape($condition['to'])}";
                }
            } elseif (isset($condition['eq'])) {
                $sql = "{$fieldName} = {$this->_db->escape($condition['eq'])}";
            } elseif (isset($condition['neq'])) {
                $sql = "{$fieldName} != {$this->_db->escape($condition['neq'])}";
            } elseif (isset($condition['like'])) {
                $sql = "{$fieldName} LIKE {$this->_db->escape('%' . $condition['like'] . '%')}";
            } elseif (isset($condition['nlike'])) {
                $sql = "{$fieldName} NOT LIKE {$this->_db->escape('%' . $condition['nlike'] . '%')}";
            } elseif (isset($condition['rlike'])) {
                $sql = "{$fieldName} RLIKE {$this->_db->escape('%' . $condition['rlike'] . '%')}";
            } elseif (isset($condition['in'])) {
                $sql = "{$fieldName} in ({$this->_db->escape($condition['in'])})";
            } elseif (isset($condition['nin'])) {
                $sql = "{$fieldName} not in ({$this->_db->escape($condition['nin'])})";
            } elseif (isset($condition['is'])) {
                $sql = "{$fieldName} is {$this->_db->escape($condition['is'])}";
            } elseif (isset($condition['notnull'])) {
                $sql = "{$fieldName} is NOT NULL";
            } elseif (isset($condition['null'])) {
                $sql = "{$fieldName} is NULL";
            } elseif (isset($condition['moreq'])) {
                $sql = "{$fieldName} >= {$this->_db->escape($condition['moreq'])}";
            } elseif (isset($condition['gt'])) {
                $sql = "{$fieldName} > {$this->_db->escape($condition['gt'])}";
            } elseif (isset($condition['lt'])) {
                $sql = "{$fieldName} < {$this->_db->escape($condition['lt'])}";
            } elseif (isset($condition['gteq'])) {
                $sql = "{$fieldName} >= {$this->_db->escape($condition['gteq'])}";
            } elseif (isset($condition['lteq'])) {
                $sql = "{$fieldName} <= {$this->_db->escape($condition['lteq'])}";
            } elseif (isset($condition['finset'])) {
                $sql = "find_in_set({$this->_db->escape($condition['finset'])}, {$fieldName})";
            } else {
                $orSql = array();
                foreach ($condition as $orCondition) {
                    $orSql[] = "(" . $this->_getConditionSql($fieldName, $orCondition) . ")";
                }
                $sql = "(" . join(" OR ", $orSql) . ")";
            }
        } else {
            $sql = "{$fieldName} = {$this->_db->escape($condition)}";
        }
        return $sql;
    }

    /**
     * Add field to filter
     *
     * @param $field
     * @param null $value
     * @return $this
     */
    public function addFieldToFilter($field, $value = null)
    {
        $this->_db->where($this->_getConditionSql($field, $value));
        return $this;
    }

    /**
     * Collection limit and offset
     *
     * @param $limit
     * @param int $offset
     */
    public function limit($limit, $offset = 0)
    {
        $this->_db->limit($limit, $offset);
    }

    public function join($table, $cond, $type = '', $escape = NULL)
    {
        $this->_db->join($table, $cond, $type, $escape);
    }
}