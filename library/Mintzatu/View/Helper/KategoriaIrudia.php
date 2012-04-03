<?php
/**
 * Kategoria irudiak idazteko
 * @author Arkaitz Etxeberria
 */
class Mintzatu_View_Helper_KategoriaIrudia extends Zend_View_Helper_Abstract
{
    public function KategoriaIrudia($lekua, $tamaina = 'lekua', $profilaLink = false)
    {
        $html = '';
        if ($profilaLink) {
            $html .= '<a href="' .$this->view->url(array(
                'controller' => 'lekuak',
                'action' => 'ikusi',
                'lekua' => $lekua->getUrl() 
            ),'',true).'">';
        }
        if ($lekua->getKategoriak()->getIrudiIzena()) {
            $html .= '<img src="'.$this->view->url(array(
                'controller' => 'lekuak',
                'action' => 'kategoria-irudia',
                'kategoria' => $lekua->getKategoriak()->getUrl(),
                'neurria' => $tamaina
            ),'',true).'" />';
        }
        if ($profilaLink) {
            $html .= '</a>';
        }
        return $html;
    }
}