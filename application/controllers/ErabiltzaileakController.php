<?php

class ErabiltzaileakController extends Zend_Controller_Action
{

    protected $_erabilMapper = null;
    protected $_lagunMapper = null;
    protected $_checkMapper = null;
    protected $_db = null;
    protected $_auth = null;
    
    
    protected $_lagunakLimit;
    protected $_lagunakOffset;
    protected $_lagunakDisableSearch;
    
    protected $_denakIkusi;

    public function init()
    {
        $this->_erabilMapper = new Mappers\Sql\Erabiltzaileak;
        $this->_lagunMapper = new Mappers\Sql\RelErabiltzaileak;
        $this->_checkMapper = new Mappers\Sql\Checks;
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_auth = Zend_Auth::getInstance();
        $this->_helper->ironContextSwitch()
        ->addActionContext('lagun-egin', 'json')
        ->initContext();
    }

    public function postDispatch()
    {
        if (!isset($this->view->headTitle)) {
            $this->view->headTitle = $this->view->title;
        }
        
    }

    public function indexAction()
    {
        // action body
    }

    public function azkenLekuakAction()
    {
        $erabil = $this->getRequest()->getParam('erabiltzailea', '');
        $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $erabil);
        if ($erabiltzailea && $erabiltzailea->getAktibatua() === '1') {
            $this->view->checks = $this->_checkMapper->fetchList('id_erabiltzaile = '.$erabiltzailea->getIdErabiltzaile(),null, 5);
        }
    }
    
    public function profilaAction()
    {
        $this->view->flashmezua = $this->_helper->FlashMessenger->getMessages('erabil');
        $jabea = false;
        $erabil = $this->getRequest()->getParam('erabiltzailea', '');
        $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $erabil);
        if ($erabiltzailea && $erabiltzailea->getAktibatua() === '1') {
            if ($this->_auth->hasIdentity()) {
                if ($this->_auth->getIdentity() === $erabil) {
                    $jabea = true;
                }
            }
            $this->view->jabea = $jabea;
            $this->view->erabiltzailea = $erabiltzailea;
            $this->view->title = $erabiltzailea->getIzena() . ' ' . $erabiltzailea->getAbizenak();
            
            $sariak = array();
            $select = "SELECT count(*) as count , id_erabiltzaile as id FROM `lekuak` GROUP BY id_erabiltzaile ORDER BY count DESC LIMIT 1";
            $query = $this->_db->query($select);
            $row =  $query->fetch();
            if ($row['id'] == $erabiltzailea->getIdErabiltzaile()) {
                $sariak['exploratzailea'] = array(
                    'irudia' => 'exploratzailea.png',
                    'mezua' => 'Exploratzailea sariaren jabea zara. Horrela jarraitu nahi baduzu, segi leku berriak sartzen.',
                    'titulua' => 'Exploratzailea'
                );
            } else {
                $sariak['exploratzailea'] = array(
                    'irudia' => 'exploratzaileaOFF.png',
                    'mezua' => 'Exploratzailea saria irabazi nahi baduzu, azken astean leku gehien sortu beharko dituzu.',
                    'titulua' => 'Exploratzailea'
                );
            }
            
            $sel = "SELECT COUNT(DISTINCT(id_lekua)) AS count, id_erabiltzaile AS id FROM checks GROUP BY id_erabiltzaile ORDER BY count DESC LIMIT 1";
            $query = $this->_db->query($select);
            $row =  $query->fetch();
            if ($row['id'] == $erabiltzailea->getIdErabiltzaile()) {
                $sariak['erregea'] = array(
                                'irudia' => 'errege.png',
                                'mezua' => 'Euskal Nagusi sari zeurea da. Sariaren jabea izaten jarraitzeko leku desberdin askotan egon beharko duzu.',
                                'titulua' => 'Euskal Nagusia'
                );
            } else {
                $sariak['erregea'] = array(
                                'irudia' => 'erregeOFF.png',
                                'mezua' => 'Euskal Nagusi saria lortzeko, leku desberdinetan gehien egon beharko duzu.',
                                'titulua' => 'Euskal Nagusia'
                );
            }
            
            for ($i = 1; $i < 4; $i++) {
                $sariak[] = array(
                    'irudia' => 'sekretua.png',
                    'mezua' => 'Sari hau sekretua da. Jarraitu Mintzatu sarea erabiltzen sari hau desblokeatzeko.',
                    'titulua' => 'Sekretua'
                );
            }
            
            $this->view->sariak = $sariak;
            $this->render('profila');
            
            $this->_lagunakLimit = 5;
            $this->_lagunakOffset = 0;
            $this->_lagunakDisableSearch = true;
            $this->_denakIkusi = true;
            
            $this->lagunakAction();
            
            $this->render('lagunak');
            
            $this->azkenLekuakAction();
            
            $this->render('azkenLekuak');
            
            $this->view->title = $erabiltzailea->getIzena() . ' ' . $erabiltzailea->getAbizenak();
            
        }
        
    }

    public function irudiaAction()
    {
        $erabil = $this->_erabilMapper->findOneByField('erabiltzailea', $this->getRequest()->getParam('erabiltzailea'));
        if ($erabil) {
            if (!$erabil->getIrudiIzena()) {
                $this->_forward('error','error');   
            }
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout()->disableLayout();
            $neurria = $this->getRequest()->getUserParam('neurria','normal');
            
            $dirhome = APPLICATION_PATH . '/../public/data/mintzatu_erabiltzaileak.plt/';
            $path = $this->_helper->karmaDocPath($dirhome, $erabil->getIdErabiltzaile());
            $thumbsPath = APPLICATION_PATH . '/../public/data/mintzatu_erabiltzaileak.plt/thumbs/';
            $pathToFile = $thumbsPath . $neurria . '_' .$erabil->getIrudiIzena();
            if (!file_exists($path)) {
                $path = APPLICATION_PATH . '/../public/img/default.png';
            }
            
            if (!file_exists($pathToFile)) {
                $irudia = new Mintzatu_Model_Irudi();
                $irudia->setData(file_get_contents($path));
                switch ($neurria){
                    case 'profila':
                        $irudia->getDefaultSize();
                        break;
                    case 'txikia':
                        $irudia->getTxikiSize();
                        break;
                }
                $irudia->saveImage($pathToFile);
            }
            
            $irudi['filename'] = $erabil->getIrudiIzena();
            $irudi['disposition'] = 'inline';
            
            $this->_helper->sendFileToClient($pathToFile, $irudi);
        } else {
            $this->_forward('error', 'error');
        }
        
    }

    public function aldatuAction()
    {
        if (!$this->_auth->hasIdentity()) {
             $this->_redirect($this->view->serverUrl($this->view->url(array(
                'controller' => 'kontuak',
                'action' => 'sartu'
            ), '', true)));
        }
        $this->view->title = 'Profila Aldatu';
        $erabil = $this->_erabilMapper->findOneByField('erabiltzailea', $this->getRequest()->getParam('erabiltzailea'));
        $irudiIzena = $erabil->getIrudiIzena();
        if ($erabil) {
            if ($this->_auth->hasIdentity()) {
                if ($this->_auth->getIdentity() !== $erabil->getErabiltzailea()) {
                    $this->_forward('error', 'error');
                }
            } else {
                $this->_forward('error', 'error');
            }
            $urlIrudia = false;
            if ($erabil->getIrudiIzena()) {
                $urlIrudia = $this->view->serverUrl($this->view->baseUrl('/erabiltzaileak/irudia/erabiltzailea/' . $erabil->getErabiltzailea() . '/neurria/normal')); 
            }
            $aldatuForm = new Application_Form_ProfilaAldatu($urlIrudia);
            if ($this->getRequest()->isPost()) {
                $datuak = $this->getRequest()->getPost();
                if (array_key_exists('Ezeztatu', $datuak)) {
                    $this->_redirect($this->view->serverUrl($this->view->url(array(
                        'controller' => 'erabiltzaileak',
                        'action' => 'profila',
                        'erabiltzailea' => $erabil->getErabiltzailea()  
                    ),'',true)));
                }
                $erabil->setIzena($datuak['izena'])
                    ->setAbizenak($datuak['abizenak'])
                    ->setJaiotzeData($datuak['jaiotze_data'])
                    ->setDeskribapena($datuak['deskribapena'])
                    ->setHerria($datuak['herria'])
                    ->setPosta($datuak['posta'])
                    ->setErabiltzailea($datuak['erabiltzailea'])
                    ->setFacebook($datuak['facebook'])
                    ->setTwitter(str_replace('@','',$datuak['twitter']))
                    ->setAldaketa(date('Y-m-d'));
                if (!$erabil->isValid()) {
                    $erroreak = $erabil->getValidator()->getErrorMessages();
                    foreach ($erroreak as $datua => $errorea) {
                        $aldatuForm->getElement($datua)->setErrors($errorea);
                    }
                    $this->view->form = $aldatuForm->populate($datuak);
                } else {
                    $aldatuForm->irudia->receive();
                    var_dump("hola");
                    $izena = $aldatuForm->irudia->getFileName();
                    if (!empty($izena[0])) {
                        $erabil->setIrudiIzena(str_replace('/tmp/', '', $izena));
                        $erabil->setIrudiTamaina($aldatuForm->irudia->getFileSize());
                        $erabil->setIrudiMota($aldatuForm->irudia->getMimeType());
                        $erabil->irudiaGorde($erabil->getIdErabiltzaile(), $izena);
                        $dir = APPLICATION_PATH . '/../public/data/mintzatu_erabiltzaileak.plt/thumbs/';
                        $handle = opendir($dir);
                        while ($file = readdir($handle))  {
                            if (is_file($dir.$file)) {
                                unlink($dir.$file);
                            }
                        }
                        
                    }
                    $erabil->save();
                    if ($this->_auth->getIdentity() !== $erabil->getErabiltzailea()) {
                        $this->_auth->clearIdentity();
                        $adaptadorea = new Zend_Auth_Adapter_DbTable(
                            Zend_Db_Table::getDefaultAdapter(),
                            'erabiltzaileak',
                            'erabiltzailea',
                            'pasahitza',
                            ''
                        );
                        $adaptadorea->setIdentity($erabil->getErabiltzailea())
                            ->setCredential($erabil->getPasahitza());
                        $this->_auth->authenticate($adaptadorea);
                        $session = new Zend_Session_Namespace('Zend_Auth');
                        $session->setExpirationSeconds(24*3600);
                    }
                    $this->_helper->FlashMessenger('Profila arazo barik aldatu da.', 'erabil');
                    $this->_redirect($this->view->serverUrl($this->view->url(array(
                        'controller' => 'erabiltzaileak',
                        'action' => 'profila',
                        'erabiltzailea' => $erabil->getErabiltzailea()  
                    ),'',true)));
                }
            } else {
                $this->view->form = $aldatuForm->populate($erabil->toArray());
            }
        } else {
            $this->_forward('error', 'error');
        }
        
    }

    public function pasahitzaAldatuAction()
    {
        $this->view->title = 'Pasahitza Aldatu';
        $aldatuForm = new Application_Form_PasahitzaAldatu();
        $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $this->getRequest()->getParam('erabiltzailea'));
        if ($this->_auth->hasIdentity()) {
            if ($this->_auth->getIdentity() !== $erabiltzailea->getErabiltzailea()) {
                $this->_forward('error', 'error');
            }
        } else {
             $this->_redirect($this->view->serverUrl($this->view->url(array(
                'controller' => 'kontuak',
                'action' => 'sartu'
            ), '', true)));
        }
        if ($this->getRequest()->isPost()) {
            $datuak = $this->getRequest()->getPost();
            if (array_key_exists('Ezeztatu', $datuak)) {
                $this->_redirect($this->view->serverUrl($this->view->url(array(
                    'controller' => 'erabiltzaileak',
                    'action' => 'profila',
                    'erabiltzailea' => $erabiltzailea->getErabiltzailea()  
                ),'',true)));
            }
            $pasahitzZaharra = $erabiltzailea->cifrar($datuak['pasahitza'], substr($erabiltzailea->getPasahitza(), 3, 8));
            if (empty($datuak['pasahitza'])) {
                $aldatuForm->getElement('pasahitza')->setErrors(array('Pasahitz Zaharra sartzea beharrezkoa da.'));
                $this->view->form = $aldatuForm->populate($datuak);
            } elseif ($pasahitzZaharra !== $erabiltzailea->getPasahitza()) {
                $aldatuForm->getElement('pasahitza')->setErrors(array('Sartutako Pasahitza ez da zuzena.'));
                $this->view->form = $aldatuForm->populate($datuak);
            } elseif (strlen($datuak['pasahitzBerria']) < 6) {
                $aldatuForm->getElement('pasahitzBerria')->setErrors(array('Pasahitza 6 karaktere baino luzeago izan behar da.'));
                $this->view->form = $aldatuForm->populate($datuak);
            } elseif (!preg_match("/^[a-zA-Z0-9]*$/",$datuak['pasahitzBerria'])) {
                $aldatuForm->getElement('pasahitzBerria')->setErrors(array('Pasahitzak hizkiak eta zenbakiak bakarrik eduki ahal ditu.'));
                $this->view->form = $aldatuForm->populate($datuak);
            } elseif ($datuak['pasahitzBerria'] !== $datuak['pasahitzakonfirmatu']) {
                $aldatuForm->getElement('pasahitzakonfirmatu')->setErrors(array('Pasahitzak desberdinak dira, saiatu berriro.'));
                $this->view->form = $aldatuForm->populate($datuak);
            } else {
                $erabiltzailea->setPasahitza($erabiltzailea->cifrar($datuak['pasahitzBerria']));
                $erabiltzailea->save();
                $this->_helper->FlashMessenger('Pasahitza arazo barik aldatu da.', 'erabil');
                $adaptadorea = new Zend_Auth_Adapter_DbTable(
                    Zend_Db_Table::getDefaultAdapter(),
                    'erabiltzaileak',
                    'erabiltzailea',
                    'pasahitza',
                    'ENCRYPT(?, SUBSTR(pasahitza, 1, 12))'
                );
                $this->_redirect('/erabiltzaileak/profila/erabiltzailea/' . $erabiltzailea->getErabiltzailea());
            }
        } else {
            if ($erabiltzailea) {
                if ($erabiltzailea->getErabiltzailea() === $this->_auth->getIdentity()) {
                    $this->view->form = $aldatuForm;
                } else {
                    $this->_forward('error', 'error');
                }
            } else {
                $this->_forward('error', 'error');
            }
        }
        
    }

    public function lagunakAction()
    {
        $limit = $this->_lagunakLimit? $this->_lagunakLimit : null;
        $offset = $this->_lagunakOffset? $this->_lagunakOffset : null;
        
        $this->view->flashmezua = $this->_helper->FlashMessenger->getMessages('erabil');
        $this->view->title = 'Lagunak';
        $profilekoPertsona = $this->_erabilMapper->findOneByField('erabiltzailea', $this->getRequest()->getParam('erabiltzailea'));
        if (!$profilekoPertsona) {
            $this->_forward('error','error');
        } else {
            $jabea = false;
            if ($this->_auth->hasIdentity()) {
                if ($this->_auth->getIdentity() === $profilekoPertsona->getErabiltzailea()) {
                    $jabea = true;
                }
            }
            if ($jabea) {
                if ($this->_lagunMapper->countByQuery('id_erabiltzaile2 = "'. $profilekoPertsona->getIdErabiltzaile().'" AND lagunak="0"') > 0) {
                    $eskaerak = $this->_lagunMapper->fetchList('id_erabiltzaile2 = "'.$profilekoPertsona->getIdErabiltzaile().'" AND lagunak="0"', 'noiz');
                    $eskaeraGendea = array();
                    foreach ($eskaerak as $pertsonak) {
                        $eskaeraGendea[$pertsonak->getIdRel()] = $this->_erabilMapper->find($pertsonak->getIdErabiltzaile1());
                    }
                    $this->view->eskaerak = $eskaeraGendea;
                }
            }
            if ($this->_lagunMapper->countByQuery('((id_erabiltzaile1 = "'. $profilekoPertsona->getIdErabiltzaile()
                .'" OR id_erabiltzaile2 = "'. $profilekoPertsona->getIdErabiltzaile()
                .'") AND lagunak="1") OR (id_erabiltzaile1 = "'. $profilekoPertsona->getIdErabiltzaile()
                .'" AND lagunak="0")') > 0) {
                
                
                $lagunak = $this->_lagunMapper->fetchList('((id_erabiltzaile1 = "'.$profilekoPertsona->getIdErabiltzaile()
                    .'" OR id_erabiltzaile2 = "'.$profilekoPertsona->getIdErabiltzaile().'") AND lagunak="1") OR (id_erabiltzaile1 = "'. $profilekoPertsona->getIdErabiltzaile()
                .'" AND lagunak="0")', 'noiz', $limit, $offset);
                $lagunZerrenda = array();
                foreach ($lagunak as $laguna) {
                    if ($laguna->getIdErabiltzaile1() === $profilekoPertsona->getIdErabiltzaile()) {
                        $lagunZerrenda[$laguna->getIdRel()] = $this->_erabilMapper->find($laguna->getIdErabiltzaile2());
                    } else {
                        $lagunZerrenda[$laguna->getIdRel()] = $this->_erabilMapper->find($laguna->getIdErabiltzaile1());
                    }
                }
                $this->view->lagunak = $lagunZerrenda;
            } else {
                $this->view->lagunak = false;
            }
            $this->view->disableSearch = ($this->_lagunakDisableSearch == true);
            $this->view->profilekoPertsona = $profilekoPertsona;
            $this->view->jabea = $jabea;
            $this->view->title = 'Lagunak';
        }
        
    }

    public function lagunEginAction()
    {
        if (!$this->_auth->hasIdentity()) {
             $this->_redirect($this->view->serverUrl($this->view->url(array(
                'controller' => 'kontuak',
                'action' => 'sartu'
            ), '', true)));
        }
        $nor = $this->_erabilMapper->findOneByField('erabiltzailea', $this->getRequest()->getParam('erabiltzailea',''));
        $nork = $this->_erabilMapper->findOneByField('erabiltzailea', $this->_auth->getIdentity());
        if (!$nor || !$nork) {
            $this->_forward('error','error');
        }
        $erlazioBerria = new Mintzatu_Model_RelErabiltzaileak();
        $erlazioBerria->setIdErabiltzaile1($nork->getIdErabiltzaile())
            ->setIdErabiltzaile2($nor->getIdErabiltzaile())
            ->setLagunak('0');
        $erlazioBerria->save();
        if ($this->getRequest()->getParam('format' , '') == 'json') {
            $this->view->mezua = 'Lagun eskaera bidali da, beste pertsonak baiezkoa eman arte itxoin beharko duzu orain.';
        } else {
            $this->_helper->FlashMessenger('Lagun eskaera bidali da, beste pertsonak baiezkoa eman arte itxoin beharko duzu orain.', 'erabil');
            $this->_redirect($this->view->serverUrl($this->view->url(array(
                'controller' => 'erabiltzaileak',
                'action' => 'lagunak',
                'erabiltzailea' => $nork->getErabiltzailea()
            ),'',true)));
        }
        
    }

    public function lagunaDeseginAction()
    {
        if ($this->_auth->hasIdentity()) {
            $erlazioa = $this->_lagunMapper->find($this->getRequest()->getParam('erlazioa', ''));
            $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $this->_auth->getIdentity());
            if ($erabiltzailea->getIdErabiltzaile() === $erlazioa->getIdErabiltzaile1() || $erabiltzailea->getIdErabiltzaile() === $erlazioa->getIdErabiltzaile2()) {
                $erlazioa->delete();
                $this->_helper->FlashMessenger('Zuen arteko erlazioa apurtu da iada', 'erabil');
                $this->_redirect($this->view->serverUrl($this->view->url(array(
                    'controller' => 'erabiltzaileak',
                    'action' => 'lagunak',
                    'erabiltzailea' => $erabiltzailea->getErabiltzailea()
                ),'',true)));
            } else {
                $this->_forward('error', 'error');
            }
        } else {
            $this->_redirect($this->view->serverUrl($this->view->url(array(
                'controller' => 'kontuak',
                'action' => 'sartu'
            ), '', true)));
        }
        
    }

    public function lagunaOnartuAction()
    {
        if ($this->_auth->hasIdentity()) {
            $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $this->_auth->getIdentity());
            $idErlazio = $this->getRequest()->getParam('erlazioa','');
            $erlazioa = $this->_lagunMapper->find($idErlazio);
            if ($erlazioa) {
                if ($erlazioa->getIdErabiltzaile2() === $erabiltzailea->getIdErabiltzaile()) {
                    $erlazioa->setLagunak('1');
                    $erlazioa->save();
                    $this->_helper->FlashMessenger('Laguna onartu duzu');
                    $this->_redirect($this->view->serverUrl($this->view->url(array(
                        'controller' => 'erabiltzaileak',
                        'action' => 'lagunak',
                        'erabiltzailea' => $erabiltzailea->getErabiltzailea()
                    ),'',true)));
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

    public function lagunaUkatuAction()
    {
        if (!$this->_auth->hasIdentity()) {
             $this->_redirect($this->view->serverUrl($this->view->url(array(
                'controller' => 'kontuak',
                'action' => 'sartu'
            ), '', true))); 
        }
        $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $this->_auth->getIdentity());
        $idErlazio = $this->getRequest()->getParam('erlazioa','');
        $erlazioa = $this->_lagunMapper->find($idErlazio);
        if ($erlazioa && $this->_auth->hasIdentity()) {
            if ($erlazioa->getIdErabiltzaile2() === $erabiltzailea->getIdErabiltzaile()) {
                $erlazioa->setLagunak('2');
                $erlazioa->save();
                $this->_redirect($this->view->serverUrl($this->view->url(array(
                    'controller' => 'erabiltzaileak',
                    'action' => 'lagunak',
                    'erabiltzailea' => $erabiltzailea->getErabiltzailea()
                ),'',true)));
            } else {
                $this->_forward('error','error');
            }
        } else {
            $this->_forward('error','error');
        }
        
    }

    public function lagunaDesblokeatuAction()
    {
        if ($this->_auth->hasIdentity()) {
            $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $this->_auth->getIdentity());
            $idErlazio = $this->getRequest()->getParam('erlazioa');
            $erlazioa = $this->_lagunMapper->find($idErlazio);
            if ($erlazioa && $erlazioa->getIdErabiltzaile2() == $erabiltzailea->getIdErabiltzaile()) {
                $erlazioa->setIdErabiltzaile2($erlazioa->getIdErabiltzaile1())
                    ->setIdErabiltzaile1($erabiltzailea->getIdErabiltzaile())
                    ->setLagunak('0')
                    ->save();
                $this->_redirect($this->getRequest()->_requestUri);
            } else {
                $this->_forward('error', 'error');  
            }
        } else {
            $this->_redirect($this->view->serverUrl($this->view->url(array(
                'controller' => 'kontuak',
                'action' => 'sartu'
            ), '', true)));
        }
    }
    
    public function bilatuAction()
    {
        $this->view->title = 'Bilaketa';
        if ($hitza = trim($this->getRequest()->getParam('pertsona'))) {
            if ($hitza != '') {
                $hitzak = explode(' ', $hitza);
                $where = '';
                if (count($hitzak) > 1) {
                    foreach ($hitzak as $hitz) {
                        /* Lerro hau aurrerantzean erabiltzaile izenagaitik bilatu nahi eskero lagetan dot */
                        //$where .= '((erabiltzailea LIKE "%'.$hitz.'%" OR izena LIKE "%'.$hitz.'%" OR abizenak = "%'.$hitz.'%") AND aktibatua = "1") OR ';
                        $where .= '((izena LIKE "%'.$hitz.'%" OR abizenak = "%'.$hitz.'%") AND aktibatua = "1") OR ';
                    }
                    $hitzak = implode(' ', $hitzak);
                    /* Goiko bardine */
                    //$where .= '((erabiltzailea LIKE "%'.$hitzak.'%" OR izena LIKE "%'.$hitzak.'%" OR abizenak = "%'.$hitzak.'%") AND aktibatua = "1")';
                    $where .= '((izena LIKE "%'.$hitzak.'%" OR abizenak = "%'.$hitzak.'%") AND aktibatua = "1")';
                } else {
                    $where .= '(izena LIKE "%'.$hitza.'%" OR abizenak = "%'.$hitza.'%") AND aktibatua = "1"';
                }
                $bilaketaEmaitza = $this->_erabilMapper->fetchList($where);
                if ($bilaketaEmaitza) {
                    $this->view->result = $bilaketaEmaitza;
                } else {
                    $this->view->result = false;
                }
            } else {
                $this->view->result = false;
            }
            $this->view->bilaketa = $hitza;
        }
        
    }


}




