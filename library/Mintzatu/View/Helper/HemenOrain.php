<?php    
/**
 * Lekuan momentuan dauden pertsonak
 * @author arkaitz
 */
class Mintzatu_View_Helper_HemenOrain extends Zend_View_Helper_Abstract
{
    public function HemenOrain($idLeku)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        if ($auth) {
            $erabilMapper = new Mappers\Sql\Erabiltzaileak;
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            $sel = $dbAdapter->select()
                ->from('checks', array('id_erabiltzaile','id_check'))
                ->where('id_lekua = ?', $idLeku)
                ->where('noiz >= date_add(NOW(), INTERVAL -1 HOUR) AND noiz <= NOW()')
                ->distinct('id_erabiltzaile')
                ->order('noiz DESC')
                ->limit('3');
            if ($pertsonak = $dbAdapter->fetchAll($sel)) {
                $gendea = 0;
                $erabil = $erabilMapper->findOneByField('erabiltzailea', $auth);
                $html = '<div class="list-box">';
                $html .= '<h2>'.$this->view->translate('Hemen Orain').'</h2>';
                foreach ($pertsonak as $nor) {
                    if ($erabil->getIdErabiltzaile() != $nor['id_erabiltzaile']) {
                        $sel2 = $dbAdapter->select()
                            ->from('rel_erabiltzaileak')
                            ->where('lagunak <> "2"')
                            ->where('(id_erabiltzaile1 = '.$nor['id_erabiltzaile'].' AND id_erabiltzaile2 = '.$erabil->getIdErabiltzaile().') OR (id_erabiltzaile2 = '.$nor['id_erabiltzaile'].' AND id_erabiltzaile1 = '.$erabil->getIdErabiltzaile().')');
                        $lagunak = $dbAdapter->fetchRow($sel2);
                        $pertsona = $erabilMapper->find($nor['id_erabiltzaile']);
                        $html .= '<div class="pertsonaPastila">';
                        $html .= $this->view->ErabiltzaileIrudia($pertsona->getErabiltzailea(),'profila', true);
                        if (!$lagunak) {
                            $html .= '<a class="botoiak" id="lagunEginCheck" href="'.$this->view->baseUrl('/erabiltzaileak/lagun-egin/erabiltzailea/'.$pertsona->getErabiltzailea()).'">';
                            $html .= '<img class="addLaguna"  title="Lagun Egin" src="'.$this->view->baseUrl('/img/lagunaAdd.png').'" />';
                            $html .= '</a>';
                        }
                        $sel3 = $dbAdapter->select()
                            ->from('checks', 'id_check')
                            ->where('id_lekua = ?', $idLeku)
                            ->where('id_erabiltzaile = ?', $erabil->getIdErabiltzaile())
                            ->where('noiz >= date_add(NOW(), INTERVAL -1 HOUR)')
                            ->order('noiz DESC')
                            ->limit('1');
                        $checked = $dbAdapter->fetchRow($sel3);
                        if (is_array($checked)) {
                            $selCheck = $dbAdapter->select()
                            ->from('rel_checks')
                            ->where('id_check1 = '.$nor['id_check'].' AND id_check2 = '.$checked['id_check'])
                            ->orWhere('id_check2 = '.$nor['id_check'].' AND id_check1 = '.$checked['id_check']);
                            if ($dbAdapter->fetchRow($selCheck)) {
                                $html .= '<img class="addLaguna" title="Berarekin Nago" src="'.$this->view->baseUrl('/img/lagunaRekinON.png').'" />';
                            } else {
                                $html .= '<a id="checkBerria" href="'.$this->view->url(array(
                                    'controller' => 'lekuak',
                                    'action' => 'rel-check',
                                    'nor' => $checked['id_check'],
                                    'norekin' => $nor['id_check'],
                                    'format' => 'json'
                                ),'', true).'" class="botoiak" rel="checked">';
                                $html .= '<img class="addLaguna" title="Berarekin Nago" src="'.$this->view->baseUrl('/img/lagunaRekin.png').'" />';
                                $html .= '</a>';
                            }
                        } else {
                            $html .= '<a id="checkBerria" href="" class="botoiak" rel="notChecked">';
                            $html .= '<img class="addLaguna" title="Berarekin Nago" src="'.$this->view->baseUrl('/img/lagunaRekin.png').'" />';
                            $html .= '</a>';
                        }
                        $html .= '</div>';
                        $gendea++;
                    }
                }
                $html .= '</div>';
                if ($gendea > 0) {
                    echo $html;
                }
            } 
        }
    }
}