<?php
/**
 * Application Model DbTables
 *
 * @package Mintzatu_Model
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 * @copyright Irontec - Internet y Sistemas sobre GNU/Linux
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Abstract class that is extended by all tables
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */
namespace Mappers\Sql\DbTable;
abstract class TableAbstract extends \Zend_Db_Table_Abstract
{
    /**
     * $_name - Name of database table
     *
     * @return string
     */
    protected $_name;

    /**
     * $_id - The primary key name(s)
     *
     * @return string|array
     */
    protected $_id;

    /**
     * Returns the primary key column name(s)
     *
     * @return string|array
     */
    public function getPrimaryKeyName()
    {
        return $this->_id;
    }

    /**
     * Returns the table name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->_name;
    }

    /**
     * Returns the number of rows in the table
     *
     * @return int
     */
    public function countAllRows()
    {
        $query = $this->select()->from($this->_name, 'count(*) AS all_count');
        $numRows = $this->fetchRow($query);

        return (int) $numRows['all_count'];
    }

    /**
     * Returns the number of rows in the table with optional WHERE clause
     *
     * @param $where string Where clause to use with the query
     * @return int
     */
    public function countByQuery($where = '')
    {
        $query = $this->select()->from($this->_name, 'count(*) AS all_count');

		if (is_array($where)) {

			list($where, $bind) = $where;

			$query->where($where);
			$query->bind($bind);

		} else if (! empty($where)) {

            $query->where($where);
        }

        $row = $this->getAdapter()->query($query)->fetch();

        return (int) $row['all_count'];
    }

    /**
     * Generates a query to fetch a list with the given parameters
     *
     * @param $where string Where clause to use with the query
     * @param $order string Order clause to use with the query
     * @param $count int Maximum number of results
     * @param $offset int Offset for the limited number of results
     * @return Zend_Db_Select
     */
    public function fetchList($where = null, $order = null, $count = null,
        $offset = null
    ) {
        $select = $this->select()
            				->order($order)
            				->limit($count, $offset);

		if (is_array($where)) {

			list($where, $bind) = $where;

			$select->where($where);
			$select->bind($bind);

		} else if (! empty($where)) {

            $select->where($where);
        }

        return $select;
    }
}
