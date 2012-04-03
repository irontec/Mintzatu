<?php
/**
 * Adaptador de Mysqli para resultados de con
 *
 * @author Alayn Gortazar <alayn@irontec.com>
 * @version 2.1
 * @package karma
 */

include_once(dirname(__FILE__) . '/../interfaces/interface.conResultAdapter_Interface.php');
class conResultMysqliAdapter  implements conResultAdapter_Interface
{
    private $_result = false;

    public function __construct($result)
    {
        $this->_result = $result;
    }

    public function freeResult()
    {
        return $this->_result->free_result();
    }

    public function fetchArray($resultType = 'ASSOC')
    {
        switch ($resultType) {
            case 'ASSOC':
                $resultType = MYSQLI_ASSOC;
                break;
            case 'NUM':
                $resultType = MYSQLI_NUM;
                break;
            case 'BOTH':
                $resultType = MYSQLI_BOTH;
                break;
        }
        return $this->_result->fetch_array($resultType);
    }

    public function isEmpty() {
        return $this->_result == false;
    }
}