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
class Mintzatu_Model_LekuenIrudiak extends Mintzatu_Model_Raw_LekuenIrudiak 
{
    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    public function irudiaGorde($tokia)
    {
        $dirhome = APPLICATION_PATH . '/../data/mintzatu_lekuen_irudiak.plt';
        $tokiBerria = $this->_tokiBerria($dirhome, $this->getIdIrudia());
        $this->_gorde($tokiBerria, $tokia);
        return true;
    }
    
    protected function _gorde($tokiBerria, $tokia)
    {
        $ruta = explode('/', $tokiBerria);
        unset($ruta[count($ruta)-1]);
        $ruta = implode('/', $ruta);
        if (is_dir($ruta)) {
            $fitxategia = fopen($tokiBerria, 'w');
            $irudia = file_get_contents($tokia);
            fwrite($fitxategia, $irudia);        
            fclose($fitxategia);
        } else {
            mkdir($ruta, 0755);
            $fitxategia = fopen($tokiBerria, 'w');
            $irudia = file_get_contents($tokia);
            fwrite($fitxategia, $irudia);        
            fclose($fitxategia);
        }
        
        return true;
    }
    
    protected function _tokiBerria($dirhome, $idIrudia) 
    {
        $aId = str_split((string)$idIrudia);
        array_pop($aId);
        if (!sizeof($aId)) {
            $aId = array('0');
        }
        $aId[] = $idIrudia;
        return $dirhome . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $aId);   
    }
}
