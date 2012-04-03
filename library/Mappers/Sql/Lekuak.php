<?php

/**
 * Application Model Mappers
 *
 * @package Mappers
 * @subpackage Sql
 * @author <Arkaitz Etxeberria>
 * @copyright Irontec - Internet y Sistemas sobre GNU/Linux
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Data Mapper implementation for Mintzatu_Model_Lekuak
 *
 * @package Mappers
 * @subpackage Sql
 * @author <Arkaitz Etxeberria>
 */
namespace Mappers\Sql;
class Lekuak extends Raw\Lekuak{
    
    protected $_latitude = null;
    protected $_longitude = null;
    
    public function setPositon($pos)
    {
        $this->_latitude = $pos['lat'];
        $this->_longitude = $pos['lng'];
    }
    
    public function fetchListByDistance($where = null, $order = null, $count = null,
            $offset = null
    ) {
        $txurro = 'if (  (  sin(   radians(    latitudea   )  ) *   sin(   radians('.$this->_latitude.')  ) +   cos(   radians(    latitudea   )  ) *   cos(   radians('.$this->_latitude.')  ) *  cos(   radians(    longitudea   ) -    radians('.$this->_longitude.')  )  ) > 1 ,  round(6378 * acos(1), 1) ,  round(6378 *    acos(    (    sin(     radians(      latitudea     )    ) *     sin(     radians('.$this->_latitude.')    ) +     cos(     radians(      latitudea     )    ) *     cos(     radians('.$this->_latitude.')    ) *    cos(     radians(      longitudea     ) -      radians('.$this->_longitude.')    )   )  ), 1   ) ) as distantzia';
        
        $q =  str_Replace('SELECT', 'SELECT ' . $txurro.', ', $this->getDbTable()->fetchList($where, $order, $count, $offset));
        $resultSet = $this->getDbTable()->getAdapter()->query($q);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = $this->loadModel($row, null);
            $entries[] = $entry;
        }
    
        return $entries;
    }
    
    
    /**
     * Loads the model specific data into the model object
     *
     * @param \Zend_Db_Table_Row_Abstract|array $data The data as returned from a \Zend_Db query
     * @param Mintzatu_Model_Lekuak|null $entry The object to load the data into, or null to have one created
     * @return Mintzatu_Model_Lekuak The model with the data provided
     */
    public function loadModel($data, $entry = null)
    {
        $entry = parent::loadModel($data, $entry);
        if ($this->_latitude!=null && $this->_longitude!=null) {
            if (is_array($data)) {
                $entry->setDistantzia($data['distantzia']);
            } elseif ($data instanceof \Zend_Db_Table_Row_Abstract || $data instanceof \stdClass) {
                $entry->setDistantzia($data->distantzia);
            }
        }
            
        return $entry;

    }
}
