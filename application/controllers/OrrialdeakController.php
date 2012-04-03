<?php

class OrrialdeakController extends Zend_Controller_Action
{

    protected $_orriMapper;
    protected $_db;
    
    public function init()
    {
        $this->_orriMapper = new Mappers\Sql\Orrialdeak;
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    public function indexAction()
    {
        // action body
    }

    public function ikusiAction()
    {
        $iden = $this->getRequest()->getParam('orria');
        $orria = $this->_orriMapper->findOneByField('identifikatzailea',$iden);
        if ($orria) {
            if ($iden == 'erabilpena') {
                $this->view->estadistikak = $this->getEstadistikak();
                $this->view->idatzi = true;
            }
            $this->view->orria = $orria;
        } else {
            $this->_redirect($this->view->serverUrl($this->view->baseUrl()));
        }
    }

    protected function getEstadistikak()
    {
        $datuak = array();
        $erabilKop = "SELECT count(*) as count FROM erabiltzaileak WHERE aktibatua = '1'";
        $checkHile = "SELECT COUNT(*) as count FROM  checks WHERE noiz > DATE_ADD( noiz, INTERVAL -1 MONTH )";
        $allChecks = "SELECT COUNT(*) as count FROM checks";
        $lekuKategoriko = "select k.id_kategoria as id, k.url as url, k.izena as izena, count(*) as count from lekuak l left join kategoriak k on l.id_kategoria = k.id_kategoria group by l.id_kategoria";
        $bisitak = "SELECT count(*) as count FROM contador";
        
        /* Erabiltzaile kopurua */
        $query = $this->_db->query($erabilKop);
        $row =  $query->fetch();
        $datuak['erabiltzaileKop'] = $row['count'];
        
        /* Chekin azken hilabetean */
        $query = $this->_db->query($checkHile);
        $row =  $query->fetch();
        $datuak['checkAzkenHilean'] = $row['count'];
        
        /* Check denak */
        $query = $this->_db->query($allChecks);
        $row =  $query->fetch();
        $datuak['checkGuztira'] = $row['count'];
        
        /* Bisitak lekura */
        $query = $this->_db->query($bisitak);
        $row = $query->fetch();
        $datuak['bisitak'] = $row['count'];
        
        /* Leku kategoriako */
        $query = $this->_db->query($lekuKategoriko);
        $rowSet = $query->fetchAll();
        foreach ($rowSet as $row) {
            $datuak['lekuKategoriko'][$row['izena']] = array('url' => $row['url'], 'zenbat' => $row['count'], 'id' => $row['id']);
        }
        
        return $datuak;
    }
}



