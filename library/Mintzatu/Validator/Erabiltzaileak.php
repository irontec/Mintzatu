<?php
Class Mintzatu_Validator_Erabiltzaileak extends Mintzatu_Validator_ValidatorAbstract
{
    protected $_db;
    protected $_auth;
    protected $_erabil;
    
    public function __construct()
    {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_auth = Zend_Auth::getInstance();
        $erabilMapper = new Mappers\Sql\Erabiltzaileak;
        if ($eraIzena = $this->_auth->getIdentity()) {
            $this->_erabil = $erabilMapper->findOneByField('erabiltzailea', $eraIzena);
        } else {
            $this->_erabil = false;
        }
    }

    /**
     * Izena betetzen dela baieztatu.
     * @param unknown_type $datua
     */
    protected function setIzena($datua)
    {
        if (empty($datua)) {
            $this->_errorMessages['izena'][] = 'Izena beharrezkoa da aurrera jarraitzeko.';
        }
        return $this;
    }
    
    /**
     * Posta elektronikoa benetakoa dela eta aurretik erabili barik dagoela baieztatu.
     * @param unknown_type $datua
     */
    protected function setPosta($datua)
    {
        if (empty($datua)) {
            $this->_errorMessages['posta'][] = 'Eposta datua beharrezkoa da';
        } else {
            $validator = new Zend_Validate_EmailAddress();
            if (!$validator->isValid($datua)) {
                $this->_errorMessages['posta'][] = 'Sartu duzun posta helbidea ez da zuzena.';
            } else {
                $sql = $this->_db->select();
                $sql->from('erabiltzaileak','count(*) as count')->where('posta = ?', $datua);
                if ($this->_erabil) {
                    $sql->where('posta != ?', $this->_erabil->getPosta());
                }
                $data = $this->_db->fetchRow($sql);
                if ($data['count'] != '0') {
                    $this->_errorMessages['posta'][] = 'Posta helbide hori erregistratuta dago iada.';
                }
            }
        }

        return $this;
    }
    
    /**
     * Erabiltzaile izena bakarra dela baieztatu.
     * @param unknown_type $datua
     */
    protected function setErabiltzailea($datua)
    {
        if (empty($datua)) {
            $this->_errorMessages['erabiltzailea'][] = 'Erabiltzailea datua beharrezkoa da.';
        } else {
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = $db->select()
                ->from('erabiltzaileak', 'count(*) as count')
                ->where('erabiltzailea = ?', $datua);
            if ($this->_erabil) {
                $sql->where('erabiltzailea != ?', $this->_erabil->getErabiltzailea());
            }
            $data = $db->fetchRow($sql);
            if ($data['count'] != '0') {
                $this->_errorMessages['erabiltzailea'][] = 'Erabiltzaile izen hori erabilita dago iada, saiatu beste batekin.';
            }
            
            return $this;
        }
    }
    
    protected function setPasahitza($datua)
    {
        if ($this->_erabil) {
            if ($datua === $this->_erabil->getPasahitza()) {
                return $this;
            }
        } else {
            if (empty($datua)) {
                $this->_errorMessages['pasahitza'][] = 'Pasahitza sartzea beharrezkoa da.';
            } else {
                if (strlen(trim($datua)) < 6) {
                    $this->_errorMessages['pasahitza'][] = 'Pasahitzak 6 karaktere eduki behar ditu gutxienez(hizki zein zenbaki).';
                } elseif (!preg_match('/^[a-zA-Z0-9]*$/', $datua)) {
                    $this->_errorMessages['pasahitza'][] = 'Pasahitzak hizkiak eta zenbakiak bakarrik eduki ahal ditu.';
                }
            }
        }
    }
}
