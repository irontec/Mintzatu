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
class Mintzatu_Model_Kategoriak extends Mintzatu_Model_Raw_Kategoriak 
{
    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    public function serializeData()
    {
        $array = $this->toArray();
        return $array;
    }
}
