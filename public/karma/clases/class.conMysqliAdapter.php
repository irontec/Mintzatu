<?php
/**
 * Adaptador de Mysqli para con
 *
 * @author Alayn Gortazar <alayn@irontec.com>
 * @version 2.1
 * @package karma
 */

include_once(dirname(__FILE__) . '/../interfaces/interface.conAdapter_Interface.php');
class conMysqliAdapter implements conAdapter_Interface
{
    protected $_connection;

    public function __construct($host, $user, $pass, $db, $port)
    {
        $this->_connection = new mysqli($host, $user, $pass, $db, $port);
    }

    /**
     * @return mysqli
     */
    public function getDbConnection()
    {
        return $this->_connection;
    }

    /**
     * @param String $sql
     * @return conResultMysqliAdapter
     */
    public function query($sql)
    {
        $result = $this->_connection->query($sql);
        return new conResultMysqliAdapter($result);
    }

    /**
     * @return int
     */
    public function getErrorNo()
    {
        $version = explode('.', phpversion());
        $phpVersionID = ($version[0] * 10000 + $version[1] * 100 + $version[2]);
        /* Bugfix por que en versiones anteriores a php 5.3.0 $connect_errno está roto.... */
        if ($phpVersionID <  '50300') {
            return mysqli_connect_errno();
        } else {
            return $this->_connection->errno;
        }
    }

    /**
     * @return string
     */
    public function getError()
    {
        $version = explode('.', phpversion());
        $phpVersionID = ($version[0] * 10000 + $version[1] * 100 + $version[2]);
        /* Bugfix por que en versiones anteriores a php 5.3.0 $connect_error está roto.... */
        if ($phpVersionID <  '50300') {
            return mysqli_connect_error();
        } else {
            return $this->_connection->error;
        }
    }

    public function ping()
    {
        return $this->_connection->ping();
    }

    public function escapeString($value)
    {
        return $this->_connection->real_escape_string($value);
    }

    public function getAffectedRows()
    {
        return $this->_connection->affected_rows;
    }

    public function getLastInsertId()
    {
        return $this->_connection->insert_id;
    }

    public function close()
    {
        return $this->_connection->close();
    }
}
