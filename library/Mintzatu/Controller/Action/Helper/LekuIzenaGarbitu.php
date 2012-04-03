<?php
require_once('Zend/Controller/Action/Helper/Abstract.php');

/**
 * Lekuen izena URL formatora pasatzeko funtzioa 
 * @author Arkaitz Etxeberria <arkaitz@irontec.com>
 */

class Mintzatu_Controller_Action_Helper_LekuIzenaGarbitu extends Zend_Controller_Action_Helper_Abstract
{
    protected $_lekuMapper;
    
    public function __construct() {
        $this->_lekuMapper = new Mappers\Sql\Lekuak;    
    }
    
    public function lekuIzenaGarbitu($izena, $id)
    {
        $extra = "";
        $cont = 1;
        do {
            $izenGarbitua = $this->_garbitu($izena) . $extra;
            $extra = "_".$cont++;
            if ($id) {
                $lekuak = $this->_lekuMapper->fetchList('id_lekua <> '.$id.' AND url = "'.$izenGarbitua.'"','',1);
            } else {
                $lekuak = $this->_lekuMapper->findOneByField('url', $izenGarbitua);
            }
        } while ($lekuak);
        
        return $izenGarbitua;
        
    }
    
    protected function _garbitu($str, $espacios = false,$singleUnderScore = false)
    {
        /* Set character encoding detection order */
        $ary[] = "UTF-8";
        $ary[] = "ISO-8859-1";
        mb_detect_order($ary);

        $str = html_entity_decode($str);

        if ($espacios) $pregStr = "/([^a-z0-9_ ])/";
        else $pregStr = "/([^a-z0-9_])/";

        $car = array(
            "á"=>"a","é"=>"e","í"=>"i","ó"=>"o","ú"=>"u",
            "Á"=>"A","É"=>"E","Í"=>"I","Ó"=>"O","Ú"=>"U",
            "ñ"=>"n","Ñ"=>"N","ü"=>"u","Ü"=>"U","\""=>"","'"=>"","-"=>"_");
        $aKeys = array_keys($car);
        // Get the strict encodign of the passed string and array keys
        $encodingStr = mb_detect_encoding($str, mb_detect_order(), true);
        $encondingKeys = mb_detect_encoding($aKeys[0], mb_detect_order(), true);
        /*
         * Encoding bug fix:
         *  If encodings are different (probably because it comes from image or file upload),
         *  change the string encoding.
         */
        if ($encodingStr != $encondingKeys && $encodingStr == "ISO-8859-1" && $encondingKeys == "UTF-8") {
            $str = utf8_encode($str);
        }
        $intermedio = mb_strtolower(str_replace($aKeys, $car, $str));
        $retorno = preg_replace($pregStr, "_", $intermedio);
        if ($singleUnderScore) {
                $retorno = preg_replace("/_+/", "_", $retorno);
                if (substr($retorno, 0, 1) == "_") $retorno = substr($retorno, 1);
                if (substr($retorno, -1) == "_") $retorno = substr($retorno, 0, -1);
        }
        return $retorno;

    }
    
    public function direct($izena, $id = false)
    {
        return $this->lekuIzenaGarbitu($izena, $id);
    }
}