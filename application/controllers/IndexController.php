<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->headScript()->appendFile('http://maps.google.com/maps/api/js?sensor=false');
    }

    public function indexAction()
    {
        $aktibitateMapper = new Mappers\Sql\Aktibitatea;
        $this->view->aktibitatea = $aktibitateMapper->fetchList('1=1','noiz DESC', 5);
        $kategoriMapper = new Mappers\Sql\Kategoriak;
        $this->view->kategoriak = $kategoriMapper->fetchList('1=1', 'izena ASC'); 
        $this->view->title = 'Euskal guneak aurkitu Mintzatu bilatzailearekin';
    }

    public function postDispatch()
    {
        if (!isset($this->view->headTitle)) {
            $this->view->headTitle = $this->view->title;
        }
    }
}