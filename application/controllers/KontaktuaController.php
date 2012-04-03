<?php

class KontaktuaController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->title = 'Kontaktua';
        $kontaktua = new Application_Form_Kontaktua();
        if ($this->getRequest()->isPost()) {
            $datuak = $this->getRequest()->getPost();
            $kontaktuBerria = new Mintzatu_Model_Kontaktua();
            $kontaktuBerria->setIzena($datuak['izena'])
                ->setPosta($datuak['posta'])
                ->setMezua($datuak['mezua']);
            if (!$kontaktuBerria->isValid()) {
                $kontaktua->isValid($datuak);
                $erroreak = $kontaktuBerria->getValidator()->getErrorMessages();
                foreach ($erroreak as $datua => $errorea) {
                    $kontaktua->getElement($datua)->setErrors($this->view->translate($errorea));
                }
                $this->view->form = $kontaktua->populate($datuak);
            } else {
                if (!$kontaktua->isValid($datuak)) {
                    $this->view->form = $kontaktua->populate($datuak);
                } else {
                    $kontaktuBerria->save();
                    $this->view->mezua = $this->view->translate('Zure mezua arazo barik jaso dugu, laster batean erantzungo dizugu zerbait. Eskerrikasko gurekin harremanetan jartzearren.');
                    $this->view->form = $kontaktua;
                }    
            }
        } else {
            $this->view->form = $kontaktua;
        }
            
    }
    
    public function postDispatch()
    {
        if (!isset($this->view->headTitle)) {
            $this->view->headTitle = $this->view->title;
        }
        
    }


}

