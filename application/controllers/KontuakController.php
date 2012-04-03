<?php

class KontuakController extends Zend_Controller_Action
{
    protected $_auth;
    protected $_erabilMapper;

    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();
        $this->_erabilMapper = new Mappers\Sql\Erabiltzaileak;
        $this->_helper->ironContextSwitch()
            ->addActionContext('sartu','json')
            ->addActionContext('erregistratu', 'json')
            ->initContext();;
    }

    public function indexAction()
    {
        $this->_redirect('/');
    }

    public function postDispatch()
    {
        if (!isset($this->view->headTitle)) {
            $this->view->headTitle = $this->view->title;
        }
    }

    public function erregistratuAction()
    {
        if ($erabil = $this->_auth->getIdentity()) {
            $this->_redirect('/');
        }
        $formulakia = new Application_Form_Erregistratu();
        if ($this->getRequest()->isPost()) {
            $datuak = $this->getRequest()->getPost();
            $erabiltzailea = new Mintzatu_Model_Erabiltzaileak();
            $erabiltzailea
                ->setIzena($datuak['izena'])
                ->setAbizenak($datuak['abizenak'])
                ->setJaiotzeData($datuak['jaiotzeData'])
                ->setDeskribapena($datuak['deskribapena'])
                ->setHerria($datuak['herria'])
                ->setPosta($datuak['posta'])
                ->setErabiltzailea($datuak['erabiltzailea'])
                ->setPasahitza($datuak['pasahitza'])
                ->setFacebook($datuak['facebook'])
                ->setTwitter(str_replace('@','',$datuak['twitter']))
                ->setAlta(date('Y-m-d'))
                ->setAldaketa(date('Y-m-d'))
                ->setGiltza($erabiltzailea->cifrar($datuak['posta']))
                ->setAktibatua('0');
            
            if (!$erabiltzailea->isValid()) {
                $erroreak = $erabiltzailea->getValidator()->getErrorMessages();
                foreach ($erroreak as $datua => $errorea) {
                    $formulakia->getElement($datua)->setErrors($errorea);
                }
                if ($datuak['pasahitza'] != $datuak['pasahitzakonfirmatu']) {
                    $formulakia->getElement('pasahitzakonfirmatu')->setErrors(array('Pasahitzak desberdinak dira, saiatu berriro.'));
                }
                $formulakia->populate($datuak);
                $this->view->form = $formulakia;
                $this->view->success = false;
                $this->view->erroreak = $erroreak;
            } elseif ($datuak['pasahitza'] != $datuak['pasahitzakonfirmatu']) {
                $formulakia->getElement('pasahitzakonfirmatu')->setErrors(array('Pasahitzak desberdinak dira, saiatu berriro.'));
                $formulakia->populate($datuak);
                $this->view->form = $formulakia;
                $this->view->success = false;
                $this->view->erroreak = array('Pasahitzak desberdinak dira, saiatu berriro.');
            } else {
                $gorde = false;
                $formulakia->irudia->receive();
                $izena = $formulakia->irudia->getFileName();
                if (!empty($izena[0])) {
                    $erabiltzailea->setIrudiIzena(str_replace('/tmp/', '', $izena));
                    $erabiltzailea->setIrudiTamaina($formulakia->irudia->getFileSize());
                    $erabiltzailea->setIrudiMota($formulakia->irudia->getMimeType());
                    $gorde = true;
                }
                $erabiltzailea->setPasahitza($erabiltzailea->cifrar($datuak['pasahitza']));
                $erabiltzailea->save();
                if ($gorde === true) {
                    $erabiltzailea->irudiaGorde($erabiltzailea->getIdErabiltzaile(), $izena);    
                }
                $erabiltzailea->altaMezuaBidali($this->view->serverUrl($this->view->baseUrl('/')));
                $this->view->success = true;
                $this->view->mezua = $this->view->translate("Dena ondo joan da eta izena eman duzu iada. Begiratu zure posta elektronikoa eta jarraitu bertan dauden pausuak zure kontua aktibatzeko.");
            }
        } else {
            $this->view->form = $formulakia;
            $this->view->title = "Izena Eman"; 
        }
        
    }

    public function altaAction()
    {
        $request = $this->getRequest();
        $giltza = $request->getParam('g', '');
        $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $request->getParam('e', ''));
        if ($erabiltzailea) {
            if ($erabiltzailea->getAktibatua() === '1') {
                $this->view->aktibatua = true;
                return;
            }
            if ($erabiltzailea->getGiltza() === $giltza) {
                $erabiltzailea->setAktibatua('1');
                $erabiltzailea->save();
                $this->view->success = true;
                $this->view->erabiltzailea = $erabiltzailea;
            }
        } else {
            $this->view->ezezaguna = true;
        }        
    }

    public function sartuAction()
    {
        $format = $this->getRequest()->getParam('format','');
        if ($this->_auth->hasIdentity()) {
            if ($format === 'json') {
                if ($this->getRequest()->isPost()) {
                    $datuak = $this->getRequest()->getPost();
                    $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $datuak['erabiltzailea']);
                    $this->view->erabiltzailea = $erabiltzailea;
                    $this->view->success = true;
                }
            } else {
                $this->_redirect('/');
            }
        } else {
            $sartuForm = new Application_Form_Sartu();
            if ($this->getRequest()->isPost()) {
                $datuak = $this->getRequest()->getPost();
                $erroreak = array();
                if (empty($datuak['pasahitza']) || empty($datuak['erabiltzailea'])) {
                    if (empty($datuak['erabiltzailea'])) {
                        $sartuForm->getElement('erabiltzailea')->setErrors(array('Erabiltzailea sartzea beharrezkoa da.'));
                        $erroreak['erabiltzailea'] = 'Erabiltzailea sartzea beharrezkoa da.';
                    } else {
                        $sartuForm->getElement('pasahitza')->setErrors(array('Pasahitza sartzea beharrezkoa da.'));
                        $erroreak['pasahitza'] = 'Pasahitza sartzea beharrezkoa da.';
                    }
                    $sartuForm->populate($datuak);
                    $this->view->sartu = $sartuForm;
                    $this->view->erroreak = $erroreak;
                    $this->view->success = false;
                } else {
                    if ($sartuForm->isValid($datuak)) {
                        if (strpos($datuak['erabiltzailea'], '@')) {
                            $db = Zend_Db_Table::getDefaultAdapter();
                            $sql = $db->select()->from('erabiltzaileak','erabiltzailea')->where('posta = ?', $datuak['erabiltzailea']);
                            $erabil = $db->fetchRow($sql);
                            $datuak['erabiltzailea'] = $erabil['erabiltzailea'];
                            $iden = 'posta';
                        } else {
                            $iden = 'erabiltzailea';
                        }
                        
                        $adaptadorea = new Zend_Auth_Adapter_DbTable(
                            Zend_Db_Table::getDefaultAdapter(),
                            'erabiltzaileak',
                            'erabiltzailea',
                            'pasahitza',
                            'ENCRYPT(?, SUBSTR(pasahitza, 1, 12))'
                        );
                        $adaptadorea->setIdentity($datuak['erabiltzailea'])
                            ->setCredential($datuak['pasahitza']);
                        $adaptadorea->getDbSelect()->where('aktibatua = "1"');
                        $result = $this->_auth->authenticate($adaptadorea);
                        
                        $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $datuak['erabiltzailea']);
                        
                        if ($result->isValid()) {
                            $session = new Zend_Session_Namespace('Zend_Auth');
                            $session->setExpirationSeconds(24*3600*31);
                            if(array_key_exists('gogoratu', $datuak)) {
                                if ($datuak['gogoratu'] == '1') {
                                    Zend_Session::rememberMe();
                                }
                            }
                            if ($format === 'json') {
                                $erabil = $erabiltzailea->toArray();
                                $erabil['success'] = true;
                                die(json_encode($erabil));
                            } else {
                                $this->_redirect('/');
                            }
                        } else {
                            if ($erabiltzailea) {
                                if (empty($datuak['pasahitza'])) {
                                    $sartuForm->getElement('pasahitza')->setErrors(array('Pasahitza sartzea beharrezkoa da.'));
                                    $erroreak['pasahitza'] = 'Pasahitza sartzea beharrezkoa da.';
                                } else {
                                    $sartuForm->getElement('pasahitza')->setErrors(array('Pasahitza ez da zuzena.'));
                                    $erroreak['pasahitza'] = 'Pasahitza ez da zuzena.';                      
                                }
                            } else {
                                if ($iden == 'erabiltzailea') {
                                    $sartuForm->getElement('erabiltzailea')->setErrors(array('Erabiltzaile hori ez da Mintzatu sarean existitzen.'));
                                    $erroreak['erabiltzailea'] = 'Erabiltzaile hori ez da Mintzatu sarean existitzen.';
                                } else {
                                    $sartuForm->getElement('erabiltzailea')->setErrors(array('Posta helbide hori ez da Mintzatu sarean existitzen.'));
                                    $erroreak['erabiltzailea'] ='Posta helbide hori ez da Mintzatu sarean existitzen.';
                                }
                            }
                            $this->view->sartu = $sartuForm->populate($datuak);
                            $this->view->erroreak = $erroreak;
                            $this->view->success = false;
                        }
                    } else {
                        $erroreak[] = 'Pasahitza ez da zuzena';
                        $sartuForm->populate($datuak);
                        $this->view->sartu = $sartuForm;
                        $this->view->erroreak = $erroreak;
                        $this->view->success = false;
                    }
                }
            } else {
                $this->view->title = 'Saioa Hasi';
                $this->view->sartu = $sartuForm;
            }
        }
    }

    public function irtenAction()
    {
        $auth = Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::forgetMe();
        $this->_redirect('/');
    }

    public function ahaztuAction()
    {
        if (!$this->_auth->hasIdentity()) {
            $ahaztuForm = new Application_Form_Ahaztu();
            $this->view->title = 'Pasahitza Ahaztu';
            if ($this->getRequest()->isPost()) {
                $datuak = $this->getRequest()->getPost();
                $baieztatzailea = new Zend_Validate_EmailAddress();
                if ($baieztatzailea->isValid($datuak['posta'])) {
                    $erabiltzailea = $this->_erabilMapper->findOneByField('posta', $datuak['posta']);
                    if ($erabiltzailea) {
                        $erabiltzailea->ahaztuMezuaBidali($this->view->serverUrl($this->view->baseUrl('/')));
                        $this->view->success = true;
                    } else {
                        $this->view->erroreak = 'Idatzi duzun posta helbidea ez da Mintzatu sarean existitzen, saiatu berriro.';
                        $this->view->form = $ahaztuForm->populate($datuak);
                    }
                } else {
                    $this->view->erroreak = 'Idatzi duzun posta helbidea ez da zuzena, saiatu berriro.';
                    $this->view->form = $ahaztuForm->populate($datuak);
                }
            } else {
                $this->view->form = $ahaztuForm;
            }
        } else {
            $this->_forward('error', 'error');
        }
    }

    public function berreskuratuAction()
    {
        $berreskuratu = new Application_Form_Berreskuratu();
        $this->view->title = 'Pasahitza Berreskuratu';
        $erabiltzailea = $this->_erabilMapper->findOneByField('erabiltzailea', $this->getRequest()->getParam('e'));
        if ($this->getRequest()->isPost()) {
            $datuak = $this->getRequest()->getPost();
            if (strlen($datuak['pasahitza']) < 6) {
                $berreskuratu->getElement('pasahitza')->setErrors(array('Pasahitza 6 karaktere baino luzeago izan behar da.'));
                $this->view->berreskuratu = $berreskuratu->populate($datuak);
            } elseif (!preg_match("/^[a-zA-Z0-9]*$/",$datuak['pasahitza'])) {
                $berreskuratu->getElement('pasahitza')->setErrors(array('Pasahitzak hizkiak eta zenbakiak bakarrik eduki ahal ditu.'));
                $this->view->berreskuratu = $berreskuratu->populate($datuak);
            } elseif ($datuak['pasahitza'] !== $datuak['pasahitzakonfirmatu']) {
                $berreskuratu->getElement('pasahitzakonfirmatu')->setErrors(array('Pasahitzak desberdinak dira, saiatu berriro.'));
                $this->view->berreskuratu = $berreskuratu->populate($datuak);
            } else {
                $erabiltzailea->setPasahitza($erabiltzailea->cifrar($datuak['pasahitza']));
                $erabiltzailea->save();
                $adaptadorea = new Zend_Auth_Adapter_DbTable(
                    Zend_Db_Table::getDefaultAdapter(),
                    'erabiltzaileak',
                    'erabiltzailea',
                    'pasahitza',
                    'ENCRYPT(?, SUBSTR(pasahitza, 1, 12))'
                );
                $adaptadorea->setIdentity($erabiltzailea->getErabiltzailea());
                $adaptadorea->setCredential($datuak['pasahitza']);
                $result = $this->_auth->authenticate($adaptadorea);
                if ($result->isValid()) {
                    $session = new Zend_Session_Namespace('Zend_Auth');
                    $session->setExpirationSeconds(24*3600*31);
                    $this->_redirect('/erabiltzaileak/profila/erabiltzailea/' . $erabiltzailea->getErabiltzailea());
                }
            }
        } else {
            if ($erabiltzailea) {
                if ($erabiltzailea->getRekuperatu() === $this->getRequest()->getParam('g')) {
                    $this->view->berreskuratu = $berreskuratu;
                } else {
                    $this->_forward('error', 'error');
                }
            } else {
                $this->_forward('error', 'error');
            }
        }
    }

    public function barruanAction()
    {
        if ($this->_auth->hasIdentity())  {
            $json['success'] = true;
        } else {
            $json['success'] = false;
        }
        die(json_encode($json));
    }

}