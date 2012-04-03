<?php
/**
 * Adaptador de Mysqli para resultados de con
 *
 * @author Alayn Gortazar <alayn@irontec.com>
 * @version 2.1
 * @package karma
 */

include_once(dirname(__FILE__) . '/../interfaces/interface.conResultAdapter_Interface.php');
class conResultPdoMysqlAdapter implements conResultAdapter_Interface
{
    private $_result = false;

    public function __construct($result)
    {
        $this->_result = $result;
    }

    /**
     * TODO: No tengo ni idea de como hacer esto en PDO, ni si tiene sentido...
     */
    public function freeResult()
    {
        unset($this->_result);
    }

    public function fetchArray($resultType = 'ASSOC')
    {
        switch ($resultType) {
            case 'ASSOC':
                $resultType = PDO::FETCH_ASSOC;
                break;
            case 'NUM':
                $resultType = PDO::FETCH_NUM;
                break;
            case 'BOTH':
                $resultType = PDO::FETCH_BOTH;
                break;
        }
        return $this->_result->fetch($resultType);
    }

    public function isEmpty() {
        return $this->_result == false;
    }
}