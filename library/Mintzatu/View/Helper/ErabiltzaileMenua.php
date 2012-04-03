<?php
/**
 * Erabiltzaile Menua idatzi edo Login estekak erakutsi
 * @author <Arkaitz Etxeberria>
 */
class Mintzatu_View_Helper_ErabiltzaileMenua extends Zend_View_Helper_Abstract
{

    public function ErabiltzaileMenua()
    {
        require_once 'Zend/Controller/Front.php';
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $auth = Zend_Auth::getInstance();
        $html = '';
        if ($auth->hasIdentity()) {
            $erabilMapper = new Mappers\Sql\Erabiltzaileak;
            $erabiltzailea = $erabilMapper->findOneByField('erabiltzailea',$auth->getIdentity());
            $lagunMapper = new Mappers\Sql\RelErabiltzaileak;
            $lagunak = $lagunMapper->countByQuery('id_erabiltzaile2 = '. $erabiltzailea->getIdErabiltzaile().' AND lagunak="0"');
            if ($lagunak > 0) {
                $span = '<span class="eskaerak">'.$lagunak.'</span>';
            } else {
                $span = '';
            }
            $html .= '<div id="tools"><div id="tool-box"><ul class="tools-ops"><li><a href="'.$baseUrl.'/erabiltzaileak/profila/erabiltzailea/' . $erabiltzailea->getErabiltzailea() .'">Kaixo ' . utf8_encode($erabiltzailea->getIzena()).'</a></li></ul>';
            $html .= '<ul class="nav">';
            $html .= '<li><a href="'.$baseUrl.'/erabiltzaileak/profila/erabiltzailea/' . $erabiltzailea->getErabiltzailea() .'">Profila Ikusi</a></li>';
            $html .= '<li><a href="'.$baseUrl.'/erabiltzaileak/lagunak/erabiltzailea/' . $erabiltzailea->getErabiltzailea() .'">Lagunak</a>'.$span.'</li>';
            $html .= '<li><a href="'.$baseUrl.'/erabiltzaileak/aldatu/erabiltzailea/' . $erabiltzailea->getErabiltzailea() .'">Profila Aldatu</a></li>';
            $html .= '<li><a href="'.$baseUrl.'/erabiltzaileak/pasahitza-aldatu/erabiltzailea/' . $erabiltzailea->getErabiltzailea() .'">Pasahitza Aldatu</a></li>';
            $html .= '<li><a href="'.$baseUrl.'/kontuak/irten/erabiltzailea/' . $erabiltzailea->getErabiltzailea() .'">Saioa Amaitu</a></li>';
            $html .= '</ul></div><!-- #tool-box -->';
            $html .= '</div>';
        } else {
            $html .= '<div id="tools">
                <div id="tool-box">
                    <ul class="tools-ops">
                        <li><a href="'.$baseUrl.'/kontuak/sartu">Saioa Hasi</a></li>
                    </ul>
                </div>
            </div>';
        } 

        return $html;
    }
}