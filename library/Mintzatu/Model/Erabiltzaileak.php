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
class Mintzatu_Model_Erabiltzaileak extends Mintzatu_Model_Raw_Erabiltzaileak 
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
    
    public function altaMezuaBidali($baseUrl)
    {
        $mapperra = new \Mappers\Sql\Postak;
        $mezua = $mapperra->findOneByField('identifikatzailea', 'Alta');
        
        $mezuaHtml = $mezua->getMezuaHtml();
        $mezuaText = $mezua->getMezuaText();
        if (strstr($mezuaHtml, 'img')) {
            $mezuaHtml = str_replace('img src="', 'img scr="' . $baseUrl ,$mezuaHtml);
        }
        
        $izenak = array('%izena%', '%abizenak%', '%altaLink%');
        $datuak = array($this->getIzena(), $this->getAbizenak(), $baseUrl . 'kontuak/alta/?e=' . $this->getErabiltzailea() . '&g=' . $this->getGiltza());

        $mezuaHtml = str_replace($izenak, $datuak, $mezuaHtml);
        $mezuaText = str_replace($izenak, $datuak, $mezuaText);
        
        $posta = new Zend_Mail();
        $posta->setBodyText($mezuaText);
        $posta->setBodyHtml($mezuaHtml);
        $posta->setFrom('no-reply@mintzatu.com', 'Mintzatu');
        $posta->addTo($this->getPosta(), $this->getIzena() . ' ' . $this->getAbizenak());
        $posta->setSubject(utf8_decode('Mintzatu :: Alta Mezua'));
        $posta->send();
    }
    
    public function ahaztuMezuaBidali($baseUrl)
    {
        $mapperra = new \Mappers\Sql\Postak;
        $mezua = $mapperra->findOneByField('identifikatzailea', 'Ahaztu');
        
        $mezuaHtml = $mezua->getMezuaHtml();
        $mezuaText = $mezua->getMezuaText();
        
        $this->setRekuperatu(sha1(date('Y-m-d,H:i:s') . $this->getErabiltzailea()));
        $this->save();
        
        $izenak = array('%izena%', '%abizenak%', '%ahaztuLink%');
        $datuak = array($this->getIzena(), $this->getAbizenak(), $baseUrl . 'kontuak/berreskuratu/?e=' . $this->getErabiltzailea() . '&g=' . $this->getRekuperatu());
        
        $mezuaHtml = str_replace($izenak, $datuak, $mezuaHtml);
        $mezuaText = str_replace($izenak, $datuak, $mezuaText);
        
        $posta = new Zend_Mail();
        $posta->setBodyText($mezuaText);
        $posta->setBodyHtml($mezuaHtml);
        $posta->setFrom('no-reply@mintzatu.com', 'Mintzatu');
        $posta->addTo($this->getPosta(), $this->getIzena() . ' ' . $this->getAbizenak());
        $posta->setSubject(utf8_decode('Mintzatu :: Pasahitza Berreskuratu'));
        $posta->send();
    }
    
    public function irudiaGorde($idIrudia, $tokia)
    {
        $dirhome = APPLICATION_PATH . '/../public/data/mintzatu_erabiltzaileak.plt';
        $tokiBerria = $this->_tokiBerria($dirhome, $this->getIdErabiltzaile());
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
    
    protected function _random($i, $tipo=3)
    {
        $cadena="";
        for ($j=0;$j<$i;$j++) {
            switch(rand(1, $tipo)) {
                case 1: //0-9
                    $cadena.=chr(rand(48, 57));
                    break;
                case 2: //A-Z
                    $cadena.=chr(rand(65, 90));
                    break;
                case 3: //a-z
                    $cadena.=chr(rand(97, 122));
                    break;
            }
        }
        return $cadena;
    }

    public function cifrar($pass, $salt="")
    {
        $salt = ($salt=="")? $this->_random(8):$salt;
        return (crypt($pass, '$1$' . $salt . '$'));
    }
}
