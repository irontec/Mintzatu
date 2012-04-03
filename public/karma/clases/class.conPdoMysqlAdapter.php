<?php
/**
 * Adaptador de PDO para con
 *
 * @author Alayn Gortazar <alayn@irontec.com>
 * @version 2.1
 * @package karma
 */

include_once(dirname(__FILE__) . '/../interfaces/interface.conAdapter_Interface.php');
class conPdoMysqlAdapter implements conAdapter_Interface
{
    protected $_connection;

    protected $_host;
    protected $_user;
    protected $_pass;
    protected $_db;
    protected $_port;

    protected $_lastRowCount;

    public function __construct($host, $user, $pass, $db, $port)
    {
        $this->_host = $host;
        $this->_user = $user;
        $this->_pass = $pass;
        $this->_db = $db;
        $this->_port = $port;
        $this->_init();
    }

    protected function _init()
    {
        $this->_connection = new PDO(
            'mysql:' . 'host='. $this->_host . ';' . 'port=' . $this->_port . ';' . 'dbname=' . $this->_db . ';',
            $this->_user,
            $this->_pass,
            array(
                PDO::ATTR_PERSISTENT => false
//                PDO::MYSQL_ATTR_COMPRESS => true
            )
        );
    }

    /**
     * @return PDO
     */
    public function getDbConnection()
    {
        return $this->_connection;
    }

    /**
     * @param String $sql
     * @return conResultPdoMysqlAdapter
     */
    public function query($sql)
    {
        if ($result = $this->_connection->query($sql)) {
            $this->_lastRowCount = $result->rowCount();
        } else {
            $this->_lastRowCount = 0;
        }
        return new conResultPdoMysqlAdapter($result);
    }

    /**
     * @return int
     */
    public function getErrorNo()
    {
        $error = $this->_connection->errorInfo();
        return array_pop($error);
    }

    /**
     * @return String
     */
    public function getError()
    {
        $error = $this->_connection->errorInfo();
        return $error[2];
    }

    public function ping()
    {
//        $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        // check
//        try {
//            $this->_connection->query('select 1');
//            //OK
//        } catch (PDOException $e) {
//            //Oups => reconnect
//            $this->_init();
//        }
        return true;
    }

    public function escapeString($value)
    {
        /*
         * FIXME: Esto está así porque el quote añade comillas al principio y al final y eso nos jode las inserts...
         */
        return mysql_real_escape_string($value);
//        return $this->_connection->quote($value);
    }

    public function getAffectedRows()
    {
        return $this->_lastRowCount;
    }

    public function getLastInsertId()
    {
        return $this->_connection->lastInsertId();
    }

    /**
     * Siempre devolvemos false porque PDO no puede cerrar las conexiones
     * @return bool
     */
    public function close()
    {
        return false;
    }
}
