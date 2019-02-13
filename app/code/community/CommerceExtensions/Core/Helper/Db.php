<?php

class CommerceExtensions_Core_Helper_Db extends CommerceExtensions_Core_Helper_Data
{
    /**
     * @var array
     */
    protected $_tableDescription = array();

    /**
     * Formats raw values for direct save into database
     *
     * @param array  $values
     * @param string $table
     *
     * @return array
     */
    public function prepareValues(array &$values, $table)
    {
        $tableDescription = $this->getTableDescription($table);
        foreach($values as $key => &$value) {

            if(!array_key_exists($key, $tableDescription)) {
                continue;
            }

            $fieldDescription = $tableDescription[$key];
            if($fieldDescription['PRIMARY']) {
                continue;
            }

            if($fieldDescription['DATA_TYPE'] == Varien_Db_Ddl_Table::TYPE_DECIMAL) {
                $value = !is_null($value) ? $this->formatDecimal($value) : null;
            }

            if($fieldDescription['DATA_TYPE'] == Varien_Db_Ddl_Table::TYPE_INTEGER) {
                $value = !is_null($value) ? $this->formatInteger($value) : null;
            }

            /** convert boolean strings to boolean values */
            if(strtolower($value) == 'false') {
                $value = false;
            }

            if(strtolower($value) == 'true') {
                $value = true;
            }
        }
        return $values;
    }

    /**
     * Constructs SQL statement to execute a bulk INSERT ON DUPLICATE KEY UPDATE
     *
     * @param string $table
     * @param array  $rows
     * @param array  $excludeUpdateCols - columns included in this array will not be affected on UPDATE, only on INSERT
     *
     * @return null|string
     */
    public function getBulkInsertUpdateSql($table, array &$rows, array $excludeUpdateCols = array())
    {
        $sql = null;
        if(!empty($rows)) {
            reset($rows);
            $columns = array_keys(current($rows));
            $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
            $sql     = "INSERT INTO {$table} (`" . implode('`,`', $columns) . "`) VALUES ";

            $valuesSql = array();
            foreach($rows as &$row) {
                $values = array();
                foreach($row as &$value) {
                    $values[] = !is_null($value) ? "{$adapter->quote($value)}" : "NULL";
                }
                $valuesSql[] = "(" . implode(',', $values) . ")";
            }
            $sql .= implode(',', $valuesSql);

            $updatesSql = array();
            foreach($columns as &$column) {
                if(in_array($column, $excludeUpdateCols)) {
                    continue;
                }
                $updatesSql[] = "`{$column}` = VALUES(`{$column}`)";
            }

            if(!empty($updatesSql)) {
                $sql .= " ON DUPLICATE KEY UPDATE " . implode(',', $updatesSql);
            }
        }
        return $sql;
    }

    /**
     * Constructs SQL statement to execute a bulk INSERT IGNORE
     *
     * @param string $table
     * @param array  $rows
     *
     * @return null|string
     */
    public function getBulkInsertIgnoreSql($table, array &$rows)
    {
        $sql = null;
        if(!empty($rows)) {
            reset($rows);
            $columns = array_keys(current($rows));
            $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
            $sql     = "INSERT IGNORE INTO {$table} (`" . implode('`,`', $columns) . "`) VALUES ";

            $valuesSql = array();
            foreach($rows as &$row) {
                $values = array();
                foreach($row as &$value) {
                    $values[] = !is_null($value) ? "{$adapter->quote($value)}" : "NULL";
                }
                $valuesSql[] = "(" . implode(',', $values) . ")";
            }
            $sql .= implode(',', $valuesSql);
        }
        return $sql;
    }

    /**
     * Get table columns data.
     * Register the results globally so that we do not continuously re-run this query.
     *
     * @param string $table
     *
     * @return array
     */
    public function getTableDescription($table)
    {
        if(!array_key_exists($table, $this->_tableDescription)) {
            $this->_tableDescription[$table] = Mage::getSingleton('core/resource')->getConnection('core_read')->describeTable($table);
        }
        return $this->_tableDescription[$table];
    }

    /**
     * @param $table
     *
     * @return array
     */
    public function getTableFieldNames($table)
    {
        $tableInfo = $this->getTableDescription($table);
        return array_keys($tableInfo);
    }

    /**
     * Formats dates to be filtered by.
     *
     * - can accept array('from' => {$date},'to' => {$date})
     * - can accept string containing an actual date
     * - can accept values valid for use within strtotime()
     *
     * @param string|array|null $value
     *
     * @return \Varien_Object
     */
    public function prepareDateFilter($value)
    {
        if(is_array(json_decode($value, true))) {
            $value = json_decode($value, true);
        }

        $filter = array('from' => null, 'to' => null);
        if($value && $value != 'anytime') {
            if(is_string($value)) {
                $filter['from'] = $value;
                $filter['to']   = $value;
            } elseif(is_array($value)) {
                if(array_key_exists('from', $value)) {
                    $filter['from'] = $value['from'];
                }
                if(array_key_exists('to', $value)) {
                    $filter['to'] = $value['to'];
                }
            }

            /** if the from/to range is backwards, correct it */
            if(strtotime($filter['from']) > strtotime($filter['to']) && $filter['from'] && $filter['to']) {
                $from           = $filter['to'];
                $to             = $filter['from'];
                $filter['from'] = $from;
                $filter['to']   = $to;
            }

            if(!is_null($filter['from'])) {
                $date           = Varien_Date::formatDate($filter['from'], false);
                $filter['from'] = $date . ' 00:00:00';
            }
            if(!is_null($filter['to'])) {
                $date         = Varien_Date::formatDate($filter['to'], false);
                $filter['to'] = $date . ' 23:59:59';
            }
        }
        return new Varien_Object($filter);
    }

    /**
     * @param string $tablename
     *
     * @return string
     */
    public function getTableName($tablename)
    {
        return Mage::getSingleton('core/resource')->getTableName($tablename);
    }
}