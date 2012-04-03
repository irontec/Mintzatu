<?php

class LekuakController extends Zend_Controller_Action
{

    protected $_erabilMapper = null;
    protected $_lekuMapper = null;
    protected $_db = null;
    protected $_auth = null;

    public function init()
    {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_erabilMapper = new Mappers\Sql\Erabiltzaileak;
        $this->_lekuMapper = new Mappers\Sql\Lekuak;
        $this->_auth = Zend_Auth::getInstance();
        $this->_helper->ironContextSwitch()
            ->addActionContext('bilatu','json')
            ->addActionContext('datuak', 'json')
            ->addActionContext('ikusi', 'json')
            ->addActionContext('check', 'json')
            ->addActionContext('kategoriak', 'json')
            ->addActionContext('berria', 'json')
            ->addActionContext('rel-check', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        // action body
        
        
        
    }
    
    public function postDispatch()
    {
        if (!isset($this->view->headTitle)) {
            $this->view->headTitle = $this->view->title;
        }
        
    }

    public function datuakAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $json = '';
        if ($this->getRequest()->getParam('zein', '') === 'denak') {
        	$lekuak = $this->_lekuMapper->fetchAll();
        } else {
        	$lekuak = $this->_lekuMapper->fetchList('url = "'.$this->getRequest()->getParam('zein').'"');
        }
        $this->view->json = $lekuak;
    }

    public function bilatuAction()
    {
        $this->view->title = 'Bilaketa';
        $katMapper = new Mappers\Sql\Kategoriak;
        $kategoriak = $katMapper->fetchAllToArray();
        foreach ($kategoriak as $kat) {
            $katGuztiak[$kat['id_kategoria']] = $kat['izena'];
        }
        $this->view->kategoriak = $katGuztiak;
        $katId = $this->getRequest()->getParam('kat');
        $hitza = trim($this->getRequest()->getParam('lekuak', '%'));
        if ($hitza == '' || $hitza == 'lekuak bilatu') {
            if ($katId != 0) {
                $this->view->title = $katGuztiak[$katId];
            }
            
            $hitza = '%';
        } 
        $limit = 50;
        //if ($hitza != '') {
            $hitzak = explode(' ', $hitza);
            $where = '';
            if (count($hitzak) > 1) {
                $where .= '(';
                foreach ($hitzak as $hitz) {
                    $where .= 'izena LIKE "%'.$hitz.'%" OR ';
                }
                $hitzak = implode(' ', $hitzak);
                if ($katId != '0') {
                    $where .= 'izena LIKE "%'.$hitzak.'%") AND id_kategoria = "'.$katId.'"';
                } else {
                    $where .= 'izena LIKE "%'.$hitzak.'%")';
                }
            } else {
                
                if ($katId != '0' && $katId !=null) {
                    $where .= 'izena LIKE "%'.$hitza.'%" AND id_kategoria = "'.$katId.'"';
                } else {
                    $where .= 'izena LIKE "%'.$hitza.'%"';
                    $limit = 5;
                }
            }
            //$bilaketaEmaitza = $this->_lekuMapper->fetchList($where, 'distantzia ASC', 50);
            
            if (isset($_COOKIE['position'])) {


                $pos = json_decode(str_replace('\\', '', $_COOKIE['position']), true);

                $this->_lekuMapper->setPositon($pos);
                $bilaketaEmaitza = $this->_lekuMapper->fetchListByDistance($where, 'distantzia ASC', $limit);
            } else {
                $bilaketaEmaitza = $this->_lekuMapper->fetchList($where, 'id_lekua DESC', $limit);
            }
            
            if ($bilaketaEmaitza) {
                $this->view->result = $bilaketaEmaitza;
            } else {
                $this->view->result = false;
            }
            if ($hitza == '%') {
                $hitza = '';
            }
            $this->view->bilaketa = $hitza;
            $this->view->kat = $katId;
       /* } else {
            $this->view->result = false;
            $this->view->bilaketa = false;
        }*/
        if ($this->_auth->hasIdentity()) {
            $this->view->botoia = true;
        }
        
        
    }

    public function berriaAction()
    {
        $this->view->title = 'Leku Berria';
        if ($this->_auth->hasIdentity()) {
            $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $this->_auth->getIdentity());
            $berriForm = new Application_Form_LekuBerria();
            if ($this->getRequest()->isPost()) {
                $datuak = $this->getRequest()->getPost();
                $izena = $datuak['izena'];
                $lekuBerria = new Mintzatu_Model_Lekuak();
                $lekuBerria->setIzena($datuak['izena'])
                    ->setHelbidea($datuak['helbidea'])
                    ->setDeskribapena($datuak['deskribapena'])
                    ->setPostakodea($datuak['postakodea'])
                    ->setHerria($datuak['herria'])
                    ->setProbintzia($datuak['probintzia'])
                    ->setEstatua($datuak['estatua'])
                    ->setLongitudea($datuak['longitudea'])
                    ->setLatitudea($datuak['latitudea'])
                    ->setUrl($this->_helper->lekuIzenaGarbitu($datuak['izena']))
                    ->setIdKategoria($datuak['kategoria'])
                    ->setIdErabiltzaile($erabiltzailea->getIdErabiltzaile());
                    
                if ($lekuBerria->isValid() && $izena != '') {
                    $lekuBerria->save();
                    $aktibitatea = new Mintzatu_Model_Aktibitatea();
                    $aktibitatea->setAkzioa('lekuberria')
                        ->setIdLerroa($lekuBerria->getIdLekua())
                        ->setTaula('lekuak')
                        ->save();
                    if ($this->getRequest()->getParam('format') == 'json') {
                        $this->view->success = true;
                        $this->view->url = $lekuBerria->getUrl();
                        $this->view->mezua =  $this->_helper->translate('Leku berria ondo sortu da.');
                    } else {
                        $this->_helper->FlashMessenger($this->_helper->translate('Leku berria ondo sortu da.'), 'leku');
                        $this->_redirect($this->view->serverUrl($this->view->url(array(
                            'controller' => 'lekuak',
                            'action' => 'ikusi',
                            'lekua' => $lekuBerria->getUrl()
                        ), '', true)));
                    }
                } else {
                    $this->view->success = false;
                    $this->view->mezua = $this->_helper->translate('Izena sartzea beharrezkoa da, saiatu berriro.');
                    $berriForm->populate($datuak);
                }
            }
            $this->view->form = $berriForm;
        } else {
            if ($this->getRequest()->getParam('format') == 'json') {
                $this->view->success = false;
            } else {
                $this->_redirect($this->view->serverUrl($this->view->url(array(
                    'controller' => 'kontuak',
                    'action' => 'sartu'
                ), '', true)));
            }
        }
         
    }

    public function ikusiAction()
    {
        $format = $this->getRequest()->getParam('format','');
        $this->view->flashmezua = $this->_helper->FlashMessenger->getMessages('leku');
        $lekua = $this->_lekuMapper->findOneByField('url', $this->getRequest()->getParam('lekua'));
        $chekMapper = new Mappers\Sql\Checks;
        $this->view->chekinak = $chekinak = $chekMapper->fetchList('id_lekua = '.$lekua->getIdLekua(),'noiz DESC',5);
        $json = array(); 
        if ($this->_auth->hasIdentity() && $lekua) {
            $this->view->irudiaForm = new Application_Form_LekuIrudia($lekua->getUrl());
            $this->view->logeatua = true;
            $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $this->_auth->getIdentity());
            if ($lekua->getIdErabiltzaile() === $erabiltzailea->getIdErabiltzaile()) {
                $this->view->jabea = true;
            }
        }
        if ($format == 'json') {
            $irudia = "http://maps.google.com/maps/api/staticmap?center=%latitudea%,%longitudea%&zoom=15&size=300x300&maptype=roadmap\&markers=icon:%baseUrl%/img/pick.png%7C%latitudea%,%longitudea%&sensor=true";
            $arg = array('%latitudea%', '%longitudea%', '%baseUrl%');
            $replace = array($lekua->getLatitudea(), $lekua->getLongitudea(), $this->view->serverUrl($this->view->baseUrl()));
            $irudiUrl = str_replace($arg, $replace, $irudia);
            $this->view->irudia = $irudiUrl;
        }
        
        if (count($chekinak)>0) {
            $select = "select count(*) as ch, c.id_erabiltzaile , e.erabiltzailea ";
            $select.= "from checks c left join erabiltzaileak e on c.id_erabiltzaile = e.id_erabiltzaile "; 
            $select.= "where noiz > DATE_ADD(now(), INTERVAL -30 DAY) and id_lekua = ".$lekua->getIdLekua()." "; 
            $select.= "group by id_erabiltzaile order by ch desc limit 1";
            
            $query = $this->_db->query($select);
            $row =  $query->fetch();
            $this->view->nagusia = $row['erabiltzailea'];
            $this->view->nagusiaTimes = $row['ch'];
        }
        
        $this->view->success = true;
        
        $this->view->title = $lekua->getIzena() . ' :: ' . $lekua->getKategoriak()->getIzena();
        
        $this->view->lekua = $lekua;
    }
    
    public function checkAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $format = $this->getRequest()->getParam('format','');
        if ($this->_auth->hasIdentity()) {
            $lekua = $this->_lekuMapper->findOneByField('url', $this->getRequest()->getParam('lekua'));
            $url = $this->view->serverUrl($this->view->url(array(
                            'controller' => 'lekuak',
                            'action' => 'ikusi',
                            'lekua' => $lekua->getUrl()
                        ), '', true));
            $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $this->_auth->getIdentity());
            $check = new Mappers\Sql\Checks;
            $bilatu = $check->fetchList('id_erabiltzaile = '.$erabiltzailea->getIdErabiltzaile().' AND id_lekua = '.$lekua->getIdLekua(), 'noiz DESC', 1);
            $azkena = $check->fetchList('id_erabiltzaile = '.$erabiltzailea->getIdErabiltzaile(), 'noiz DESC', 1);
            if (!$bilatu) {
                if ($azkena) {
                    $noiz = $azkena->getNoiz();
                    $dif = strtotime(date("Y-m-d H:i:s")) - strtotime($noiz);
                    $difMinutu = ($dif/60);
                    if ($difMinutu > 10) {
                        $checkBarria = new Mintzatu_Model_Checks();
                        $checkBarria->setIdErabiltzaile($erabiltzailea->getIdErabiltzaile())
                            ->setNondik('web')
                            ->setIdLekua($lekua->getIdLekua())
                            ->save();
                        if ($format === 'json') {
                            $this->view->mezua = 'Dena ondo joan da, badakigu hemen zaudela iada!';
                            $this->view->success = true;
                        } else {
                            $this->_helper->FlashMessenger($this->_helper->translate('Dena ondo joan da, badakigu hemen zaudela iada!'), 'leku');
                            $this->_redirect($url);
                        }
                    } else {
                        if ($format === 'json') {
                            $this->view->mezua = 'Hegan zoaz ala? Ez tranparik egin...';
                            $this->view->success = true;
                        } else {
                            $this->_helper->FlashMessenger($this->_helper->translate('Hegan zoaz ala? Ez tranparik egin...'), 'leku');
                            $this->_redirect($url);
                        }
                    }
                } else {
                    $checkBarria = new Mintzatu_Model_Checks();
                    $checkBarria->setIdErabiltzaile($erabiltzailea->getIdErabiltzaile())
                        ->setNondik('web')    
                        ->setIdLekua($lekua->getIdLekua())
                        ->save();
                    if ($format === 'json') {
                        $this->view->mezua = 'Dena ondo joan da, badakigu hemen zaudela iada!';
                        $this->view->success = true;
                    } else {
                        $this->_helper->FlashMessenger($this->_helper->translate('Dena ondo joan da, badakigu hemen zaudela iada!'), 'leku');
                        $this->_redirect($url);
                    }
                }
            } else {
                $noiz = $bilatu->getNoiz();
                $dif = mktime() - strtotime($noiz);
                $difOrdu = ($dif/(60*60));
                if ($difOrdu > 1) {
                    $checkBarria = new Mintzatu_Model_Checks();
                    $checkBarria->setIdErabiltzaile($erabiltzailea->getIdErabiltzaile())
                        ->setIdLekua($lekua->getIdLekua())
                        ->setNondik('web')
                        ->save();
                    if ($format === 'json') {
                        $this->view->mezua = 'Dena ondo joan da, badakigu hemen zaudela iada!';
                        $this->view->success = true;
                    } else {
                        $this->_helper->FlashMessenger($this->_helper->translate('Dena ondo joan da, badakigu hemen zaudela iada!'), 'leku');
                        $this->_redirect($url);
                    }
                } else {
                    if ($format === 'json') {
                        $this->view->mezua = 'Ez da ordu bete ere igaro hemen azkenengoz egon zarenetik, itxoin egin beharko duzu hurrengo check-ina egiteko.';
                        $this->view->success = true;
                    } else {
                        $this->_helper->FlashMessenger($this->_helper->translate('Ez da ordu bete ere igaro hemen azkenengoz egon zarenetik, itxoin egin beharko duzu hurrengo check-ina egiteko.'), 'leku');
                        $this->_redirect($url);
                    }
                }
            }
        } else {
            return false;
        }
    }
    
    public function relCheckAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $nor = $this->getRequest()->getParam('nor','');
        $norekin = $this->getRequest()->getParam('norekin', '');
        if ($nor != '' && $norekin != '') {
            $this->_db->insert('rel_checks', array('id_check1' => $nor, 'id_check2' => $norekin));
            $this->view->mezua = $this->view->translate('Dena ondo joan da. Orain badakigu elkarrekin zaudetela.');
            $this->view->success = true;
        } else {
            $this->view->mezua = $this->view->translate('Akatsen bat egon da, saiatu.');
            $this->view->success = false;
        }
    }

    public function aldatuAction()
    {
        $this->view->title = 'Lekua Aldatu';
        $lekua = $this->_lekuMapper->findOneByField('url', $this->getRequest()->getParam('lekua'));
        if ($this->_auth->hasIdentity()) {
            $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $this->_auth->getIdentity());
            if ($lekua->getIdErabiltzaile() === $erabiltzailea->getIdErabiltzaile()) {
                $aldatuForm = new Application_Form_LekuBerria();
                if ($this->getRequest()->isPost()) {
                    $datuak = $this->getRequest()->getPost();
                    $lekua->setIzena($datuak['izena'])
                        ->setHelbidea($datuak['helbidea'])
                        ->setDeskribapena($datuak['deskribapena'])
                        ->setPostakodea($datuak['postakodea'])
                        ->setHerria($datuak['herria'])
                        ->setProbintzia($datuak['probintzia'])
                        ->setEstatua($datuak['estatua'])
                        ->setLongitudea($datuak['longitudea'])
                        ->setLatitudea($datuak['latitudea'])
                        ->setUrl($this->_helper->lekuIzenaGarbitu($datuak['izena'], $lekua->getIdLekua()))
                        ->setIdKategoria($datuak['kategoria'])
                        ->save();
                        $this->_helper->FlashMessenger($this->_helper->translate('Lekuaren datuak ondo aldatu dira.'), 'leku');
                        $this->_redirect($this->view->serverUrl($this->view->url(array(
                            'controller' => 'lekuak',
                            'action' => 'ikusi',
                            'lekua' => $lekua->getUrl()
                        ), '', true)));
                } else {
                    $lekuaArray = $lekua->toArray();
                    $lekuaArray['kategoria'] = $lekuaArray['id_kategoria'];
                    $this->view->form = $aldatuForm->populate($lekuaArray);
                }
                $this->view->jabea = true;
                $this->view->lekua = $lekua;
            } else {
                $this->view->jabea = false;
            }
        } else {
            $this->_redirect($this->view->serverUrl($this->view->url(array(
                'controller' => 'kontuak',
                'action' => 'sartu'
            ), '', true)));
        }
    }
    
    public function irudiBerriaAction()
    {
        if ($this->_auth->hasIdentity()) {
            $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $this->_auth->getIdentity());
            $lekua = $this->_lekuMapper->findOneByField('url', $this->getRequest()->getParam('lekua'));
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            if ($this->getRequest()->isPost()) {
                $datuak = $this->getRequest()->getPost();
                $lekuIrudiModel = new Mintzatu_Model_LekuenIrudiak($lekua->getUrl());
                $lekuIrudiModel->setIdErabiltzailea($erabiltzailea->getIdErabiltzaile())
                    ->setIdLekua($lekua->getIdLekua())
                    ->setIruzkina($datuak['iruzkina'])
                    ->save();
                $lekuIrudi = new Application_Form_LekuIrudia($lekua->getUrl());
                $lekuIrudi->irudia->receive();
                $izena = $lekuIrudi->irudia->getFileName();
                if (!empty($izena[0])) {
                    $lekuIrudiModel->setIrudiIzena(str_replace('/tmp/', '', $izena));
                    $lekuIrudiModel->setIrudiTamaina($lekuIrudi->irudia->getFileSize());
                    $lekuIrudiModel->setIrudiMota($lekuIrudi->irudia->getMimeType());
                    $lekuIrudiModel->irudiaGorde($izena);
                    $lekuIrudiModel->save();
                    $this->_helper->FlashMessenger($this->_helper->translate('Irudia arazo barik igon da.'), 'leku');
                    $this->_redirect($this->view->serverUrl($this->view->url(array(
                        'controller' => 'lekuak',
                        'action' => 'ikusi',
                        'lekua' => $lekua->getUrl()
                    ), '', true)));
                } else {
                    $this->_forward('error','error');
                }
            } else {
                $this->_forward('error','error');
            }
        } else {
            $this->_redirect($this->view->serverUrl($this->view->url(array(
                'controller' => 'kontuak',
                'action' => 'sartu'
            ), '', true)));
        }
    }
    
    public function kategoriakAction()
    {
        $katMapper = new Mappers\Sql\Kategoriak;
        $kategoriak = $katMapper->fetchList(null,'izena ASC');
        foreach ($kategoriak as $kat) {
            $kat = $kat->toArray();
            $this->_kategoriak[$kat['id_kategoria']] = $kat['izena'];
        }
        $this->view->kategoriak = $this->_kategoriak;
    }
    
    public function kategoriaIrudiaAction()
    {
        $katMapper = new Mappers\Sql\Kategoriak;
        $kategoria = $katMapper->findOneByField('url', $this->getRequest()->getParam('kategoria'));
        if ($kategoria) {
            if (!$kategoria->getIrudiIzena()) {
                $this->_forward('error','error');   
            }
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout()->disableLayout();
            $neurria = $this->getRequest()->getUserParam('neurria');
            
            $dirhome = APPLICATION_PATH . '/../data/mintzatu_kategoriak.plt/';
            $path = $this->_helper->karmaDocPath($dirhome, $kategoria->getIdKategoria());
            $thumbsPath = APPLICATION_PATH . '/../data/mintzatu_kategoriak.plt/thumbs/';
            $pathToFile = $thumbsPath . $neurria . '_' .$kategoria->getIrudiIzena();
            
            if (!file_exists($pathToFile)) {
                $irudia = new Mintzatu_Model_Irudi();
                $irudia->setData(file_get_contents($path));
                switch ($neurria){
                    case 'lekua':
                        $irudia->getLekuaSize();
                        break;
                    case 'aktibitatea':
                        $irudia->getAktibitateaSize();
                        break;
                    case 'mapa':
                        $irudia->getMapaSize();
                        break;
                }
                $irudia->saveImage($pathToFile);
            }
            
            $irudi['filename'] = $kategoria->getIrudiIzena();
            $irudi['disposition'] = 'inline';
            
            $this->_helper->sendFileToClient($pathToFile, $irudi);
        } else {
            $this->_forward('error', 'error');
        }
    }
}









