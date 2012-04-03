<?php

/**
 * Application Models
 *
 * @package Mintzatu_Model
 * @subpackage Model
 * @author <Arkaitz Etxeberria>
 * @copyright Irontec - Internet y Sistemas sobre GNU/Linux
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * 
 *
 * @package Mintzatu_Model
 * @subpackage Model
 * @author <Arkaitz Etxeberria>
 */
class Mintzatu_Model_Lekuak extends Mintzatu_Model_Raw_Lekuak 
{
    public $_Distantzia = null;
    
    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    
    
    public function setDistantzia($data)
    {
        $this->_Distantzia = $data;
        return $this; 
    }
    
    public function getDistantzia()
    {
        return $this->_Distantzia;
    }
    
    public function serializeData()
    {
        $array = $this->toArray();
        $array['kategoria'] = $this->getKategoriak()->serializeData();
        $array['erabiltzailea'] = $this->getErabiltzaileak()->serializeData();
        return $array;
    }    
    
}
