<?php
/**
 * Noiztik egin diren checkinak idazteko
 * @author <Arkaitz Etxeberria>
 */
class Mintzatu_View_Helper_Noiz extends Zend_View_Helper_Abstract
{
    public function Noiz($data)
    {
        $html = '';
        $noiz = strtotime($data);
        $diferentzia = time() - strtotime($data);
        if (ceil($diferentzia/(60*60*24*7)) > 1) {
            if (ceil($diferentzia/(60*60*24*7)) == 1) {
                return $this->view->translate('Orain dela<br />aste bete');
            } else {
                return 'Orain dela<br />'.ceil($diferentzia/(60*60*24*7)).' aste';
            }
        } elseif (ceil($diferentzia/(60*60*24)) > 1) {
            if (ceil($diferentzia/(60*60*24)) == 1) {
                return 'Orain dela<br />egun 1';
            } else {
                return 'Orain dela<br />'.ceil($diferentzia/(60*60*24)).' egun';
            }
        } elseif (ceil($diferentzia/(60*60)) > 1) {
            if (ceil($diferentzia/(60*60)) == 1) {
                return 'Oran dela<br />ordu bat';
            } else {
                return 'Orain dela<br />'.ceil($diferentzia/(60*60)).' ordu';
            }
        } elseif (ceil($diferentzia/60) > 1) {
            if (ceil($diferentzia/60) == 1) {
                return 'Orain dela<br />minutu bat';
            } else {
                return 'Orain dela<br />'.(ceil($diferentzia/60)).' minutu';
            }
        } else {
            return 'Orain bertan';
        }
        echo $data;
        
    }
}