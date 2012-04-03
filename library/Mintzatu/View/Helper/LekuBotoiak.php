<?php
/**
 * Erabiltzaile Menua idatzi edo Login estekak erakutsi
 * @author <Arkaitz Etxeberria>
 */
class Mintzatu_View_Helper_LekuBotoiak extends Zend_View_Helper_Abstract
{

    public function LekuBotoiak($lekua)
    {
        $auth = Zend_Auth::getInstance();
        $html = '';
        if ($auth->hasIdentity()) {
            $html = '<div id="lekuAukerak"><ul>';            
            $erabilMapper = new Mappers\Sql\Erabiltzaileak;
            $erabiltzailea = $erabilMapper->findOneByField('erabiltzailea',$auth->getIdentity());
            $checkMapper = new Mappers\Sql\Checks;
            $azkena = $checkMapper->fetchList('id_erabiltzaile = '.$erabiltzailea->getIdErabiltzaile(), 'noiz DESC', 1);
            
            if ($azkena 
                    && ($azkena->getIdLekua() === $lekua->getIdLekua())) {
                $noiz = $azkena->getNoiz();
                $dif = time() - strtotime($noiz);
                $difOrdu = $dif/(60*60);
                if ($difOrdu > 1) {
                    $html .= '<li><a href="" class="chekcIn link-button">'.$this->view->translate("Hemen Nago").'</a></li>';
                } else {
                    $html .= '<li><a href="" class="chekcIn link-button">'.$this->view->translate("Hemen Zaude").'</a></li>';
                }
            } else {
                $html .= '<li><a href="" class="chekcIn link-button">'.$this->view->translate("Hemen Nago").'</a></li>';
            }
            /*Irudiak igoteko*/
            //$html .= '<li><a href="" id="irudiaIgo" class="link-button">'.$this->view->translate("Irudia Igo").'</a></li>';
            if ($lekua->getIdErabiltzaile() === $erabiltzailea->getIdErabiltzaile()) {
                $html .= '<li><a class="link-button" href="'.$this->view->url(array(
                    'controller' => 'lekuak',
                    'action' => 'aldatu',
                    'lekua' => $lekua->getUrl()
                ),'',true).'">'.$this->view->translate('Datuak Aldatu').'</a></li>';
            }
            $html .= '</ul></div>';
        }
        return $html;
        
    }
                
}