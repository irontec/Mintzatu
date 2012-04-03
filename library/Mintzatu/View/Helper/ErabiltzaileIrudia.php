<?php
/**
 * Erabiltzailea irudiak idazteko
 * @author arkaitz
 */
class Mintzatu_View_Helper_ErabiltzaileIrudia extends Zend_View_Helper_Abstract
{
    public function ErabiltzaileIrudia($erabiltzaileIzena, $tamaina = 'normal', $profilaLink = false)
    {
        require_once 'Zend/Controller/Front.php';
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $html = '';
        if ($profilaLink) {
            $html .= '<a href="' .$baseUrl.'/erabiltzaileak/profila/erabiltzailea/'.$erabiltzaileIzena.'">';
        }
        $html .= '<img src="'.$baseUrl.'/erabiltzaileak/irudia/erabiltzailea/'.$erabiltzaileIzena.'/neurria/'.$tamaina.'" />';
        if ($profilaLink) {
            $html .= '</a>';
        }
        return $html;
    }
}