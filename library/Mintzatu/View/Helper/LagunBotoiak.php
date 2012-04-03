<?php
/**
 * Lagunen arteko botoiak idazteko 
 * @author <Arkaitz Etxeberria>
 */
class Mintzatu_View_Helper_LagunBotoiak extends Zend_View_Helper_Abstract
{
    public function LagunBotoiak($idErlazio = false, $idErabil = false, $jabea = false, $profilekoPertsona = false)
    {
        require_once 'Zend/Controller/Front.php';
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $auth = Zend_Auth::getInstance()->getIdentity();
        $html = '';
        
        if ($auth) {
            $erabilMapper = new Mappers\Sql\Erabiltzaileak;
            $erlazioMapper = new Mappers\Sql\RelErabiltzaileak;
            if (!$idErlazio) {
                $profilekoPertsona = $erabilMapper->find($idErabil);
                if ($auth === $profilekoPertsona->getErabiltzailea()) {
                    return;
                } else {
                    $erabiltzailea = $erabilMapper->findOneByField('erabiltzailea', $auth);
                    $erlazioa1 = $erlazioMapper->fetchList(
                        'id_erabiltzaile1 = "'.$erabiltzailea->getIdErabiltzaile()
                        .'" AND id_erabiltzaile2 = "'.$profilekoPertsona->getIdErabiltzaile()
                        .'"', null, 1);
                    $erlazioa2 = $erlazioMapper->fetchList(
                        'id_erabiltzaile2 = "'.$erabiltzailea->getIdErabiltzaile()
                        .'" AND id_erabiltzaile1 = "'.$profilekoPertsona->getIdErabiltzaile()
                        .'"', null, 1);
                    if($erlazioa1){
                        switch ($erlazioa1->getLagunak()) {
                            case '0':
                                $html .= 'Itxoitzen...';
                                break;
                            case '1':
                                $html .= '<a class="confirm" href="'.$baseUrl.'/erabiltzaileak/laguna-desegin/erlazioa/'.$erlazioa1->getIdRel().'">Laguna Desegin</a>';
                                break;
                        }
                        return $html;
                    } elseif ($erlazioa2) {
                        switch ($erlazioa2->getLagunak()) {
                            case '0':
                                $html .= '<a href="'.$baseUrl.'/erabiltzaileak/laguna-onartu/erlazioa/'.$erlazioa2->getIdRel().'">Onartu</a><br />';
                                $html .= '<a class="confirm" href="'.$baseUrl.'/erabiltzaileak/laguna-ukatu/erlazioa/'.$erlazioa2->getIdRel().'">Ukatu</a>';
                                break;
                            case '1':
                                $html .= '<a class="confirm" href="'.$baseUrl.'/erabiltzaileak/laguna-desegin/erlazioa/'.$erlazioa2->getIdRel().'">Laguna Desegin</a>';
                                break;
                            case '2':
                                $html .= '<a href="'.$baseUrl.'/erabiltzaileak/laguna-desblokeatu/erlazioa/'.$erlazioa2->getIdRel().'">Desblokeatu</a>';
                                break;
                        }
                        return $html;
                    } else {
                        $html .= '<a href="'.$baseUrl.'/erabiltzaileak/lagun-egin/erabiltzailea/'.$profilekoPertsona->getErabiltzailea().'">Lagun Egin</a>';
                        return $html;
                    }
                }
            } else {
                $erabiltzailea = $erabilMapper->findOneByField('erabiltzailea', $auth);
                $erlazioa = $erlazioMapper->find($idErlazio);
                if ($jabea) {
                    if ($erabiltzailea->getIdErabiltzaile() === $erlazioa->getIdErabiltzaile1()){
                        switch ($erlazioa->getLagunak()) {
                            case '0':
                                $html .= 'Itxoitzen...';
                                break;
                            case '1':
                                $html .= '<a class="confirm" href="'.$baseUrl.'/erabiltzaileak/laguna-desegin/erlazioa/'.$erlazioa->getIdRel().'">Laguna Desegin</a>';
                                break;
                        }
                        return $html;
                    } elseif ($erabiltzailea->getIdErabiltzaile() === $erlazioa->getIdErabiltzaile2()) {
                        switch ($erlazioa->getLagunak()) {
                            case '0':
                                $html .= '<a href="'.$baseUrl.'/erabiltzaileak/laguna-onartu/erlazioa/'.$erlazioa->getIdRel().'">Onartu</a><br />';
                                $html .= '<a class="confirm" href="'.$baseUrl.'/erabiltzaileak/laguna-ukatu/erlazioa/'.$erlazioa->getIdRel().'">Ukatu</a>';
                                break;
                            case '1':
                                $html .= '<a class="confirm"  href="'.$baseUrl.'/erabiltzaileak/laguna-desegin/erlazioa/'.$erlazioa->getIdRel().'">Laguna Desegin</a>';
                                break;
                            case '2':
                                $html .= '<a href="'.$baseUrl.'/erabiltzaileak/laguna-desblokeatu/erlazioa/'.$erlazioa->getIdRel().'">Desblokeatu</a>';
                                break;
                        }
                        return $html;
                    }
                } else {
                    if ($erlazioa->getIdErabiltzaile1() !== $erabiltzailea->getIdErabiltzaile() && $erlazioa->getIdErabiltzaile2() !== $erabiltzailea->getIdErabiltzaile()) {
                        if ($erlazioa->getIdErabiltzaile1() === $profilekoPertsona->getIdErabiltzaile()) {
                            $idBestea = $erlazioa->getIdErabiltzaile2();
                        } elseif ($erlazioa->getIdErabiltzaile2() === $profilekoPertsona->getIdErabiltzaile()) {
                            $idBestea = $erlazioa->getIdErabiltzaile1();
                        }
                        $bestea = $erabilMapper->find($idBestea);
                        $idErabil = $erabiltzailea->getIdErabiltzaile();
                        if ($erlazioMapper->countByQuery('(id_erabiltzaile1 = "'.$idErabil.'" AND id_erabiltzaile2 = "'.$idBestea
                            .'") OR (id_erabiltzaile2 = "'.$idErabil.'" AND id_erabiltzaile1 = "'.$idBestea.'")')) {
                            $erlazioa = $erlazioMapper->fetchList('(id_erabiltzaile1 = "'.$idErabil.'" AND id_erabiltzaile2 = "'.$idBestea
                            .'") OR (id_erabiltzaile2 = "'.$idErabil.'" AND id_erabiltzaile1 = "'.$idBestea.'")','', 1);
                            switch ($erlazioa->getLagunak()) {
                                case '0':
                                    if ($erlazioa->getIdErabiltzaile1() === $idBestea) {
                                        $html .= '<a href="'.$baseUrl.'/erabiltzaileak/laguna-onartu/erlazioa/'.$erlazioa->getIdRel().'">Onartu</a><br />';
                                        $html .= '<a class="confirm" href="'.$baseUrl.'/erabiltzaileak/laguna-ukatu/erlazioa/'.$erlazioa->getIdRel().'">Ukatu</a>';
                                    } else {
                                        $html .= 'Itxoitzen...';
                                    }
                                    break;
                                case '1':
                                    $html .= '<a class="confirm" href="'.$baseUrl.'/erabiltzaileak/laguna-desegin/erlazioa/'.$erlazioa->getIdRel().'">Laguna Desegin</a>';
                                    break;
                                case '2':
                                    if ($erlazioa->getIdErabiltzaile1() === $idBestea) {
                                        $html .= '<a href="'.$baseUrl.'/erabiltzaileak/laguna-desblokeatu/erlazioa/'.$erlazioa->getIdRel().'">Desblokeatu</a>';
                                    }
                                    break;
                            }
                            return $html;
                        } else {
                            $bestea = $erabilMapper->find($idBestea);
                            $html .= '<a href="'.$baseUrl.'/erabiltzaileak/lagun-egin/erabiltzailea/'.$bestea->getErabiltzailea().'">Lagun Egin</a>';
                            return $html;
                        }
                    } 
                }
            }
        } else {
            return;
        }
        
    }
}