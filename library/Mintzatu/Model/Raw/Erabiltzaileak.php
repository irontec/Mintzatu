<?php
/**
 * Application Models
 *
 * @package Mintzatu_Model_Raw
 * @subpackage Model
 * @author <Lander Ontoria Gardeazabal>
 * @copyright Irontec - Internet y Sistemas sobre GNU/Linux
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
/**
 * 
 *
 * @package Mintzatu_Model
 * @subpackage Model
 * @author <Lander Ontoria Gardeazabal>
 */
class Mintzatu_Model_Raw_Erabiltzaileak extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdErabiltzaile;

    /**
     * Database var type varchar(250)
     *
     * @var string
     */
    protected $_Erabiltzailea;

    /**
     * Database var type char(50)
     *
     * @var string
     */
    protected $_Pasahitza;

    /**
     * Database var type char(50)
     *
     * @var string
     */
    protected $_Rekuperatu;

    /**
     * Database var type varchar(250)
     *
     * @var string
     */
    protected $_Izena;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Abizenak;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Herria;

    /**
     * Database var type varchar(350)
     *
     * @var string
     */
    protected $_IrudiIzena;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_IrudiTamaina;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_IrudiMota;

    /**
     * Database var type text
     *
     * @var text
     */
    protected $_Deskribapena;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Posta;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Facebook;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Twitter;

    /**
     * Database var type date
     *
     * @var string
     */
    protected $_JaiotzeData;

    /**
     * Database var type varchar(400)
     *
     * @var string
     */
    protected $_Url;

    /**
     * Database var type datetime
     *
     * @var string
     */
    protected $_Alta;

    /**
     * Database var type datetime
     *
     * @var string
     */
    protected $_Aldaketa;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Giltza;

    /**
     * Database var type enum('0','1')
     *
     * @var string
     */
    protected $_Aktibatua;



    /**
     * Dependent relation checks_ibfk_3
     * Type: One-to-Many relationship
     *
     * @var Mintzatu_Model_Checks[]
     */
    protected $_Checks;

    /**
     * Dependent relation lekuak_ibfk_1
     * Type: One-to-Many relationship
     *
     * @var Mintzatu_Model_Lekuak[]
     */
    protected $_Lekuak;

    /**
     * Dependent relation rel_erabiltzaileak_ibfk_1
     * Type: One-to-Many relationship
     *
     * @var Mintzatu_Model_RelErabiltzaileak[]
     */
    protected $_RelErabiltzaileakByIdErabiltzaile1;

    /**
     * Dependent relation rel_erabiltzaileak_ibfk_2
     * Type: One-to-Many relationship
     *
     * @var Mintzatu_Model_RelErabiltzaileak[]
     */
    protected $_RelErabiltzaileakByIdErabiltzaile2;

    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'id_erabiltzaile'=>'IdErabiltzaile',
            'erabiltzailea'=>'Erabiltzailea',
            'pasahitza'=>'Pasahitza',
            'rekuperatu'=>'Rekuperatu',
            'izena'=>'Izena',
            'abizenak'=>'Abizenak',
            'herria'=>'Herria',
            'irudi_izena'=>'IrudiIzena',
            'irudi_tamaina'=>'IrudiTamaina',
            'irudi_mota'=>'IrudiMota',
            'deskribapena'=>'Deskribapena',
            'posta'=>'Posta',
            'facebook'=>'Facebook',
            'twitter'=>'Twitter',
            'jaiotze_data'=>'JaiotzeData',
            'url'=>'Url',
            'alta'=>'Alta',
            'aldaketa'=>'Aldaketa',
            'giltza'=>'Giltza',
            'aktibatua'=>'Aktibatua',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('erabiltzaile','izena','tamaina','mota','data'));

        $this->setParentList(array(
        ));

        $this->setDependentList(array(
            'ChecksIbfk3' => array(
                    'property' => 'Checks',
                    'table_name' => 'Checks',
                ),
            'LekuakIbfk1' => array(
                    'property' => 'Lekuak',
                    'table_name' => 'Lekuak',
                ),
            'RelErabiltzaileakIbfk1' => array(
                    'property' => 'RelErabiltzaileakByIdErabiltzaile1',
                    'table_name' => 'RelErabiltzaileak',
                ),
            'RelErabiltzaileakIbfk2' => array(
                    'property' => 'RelErabiltzaileakByIdErabiltzaile2',
                    'table_name' => 'RelErabiltzaileak',
                ),
        ));
 
        $this->setOnDeleteCascadeRelationships(array(
        	'checks_ibfk_3',
        	'rel_erabiltzaileak_ibfk_1',
        	'rel_erabiltzaileak_ibfk_2'
        ));
 
        $this->setOnDeleteSetNullRelationships(array(
			'lekuak_ibfk_1'
		));

		parent::__construct();
    }




    /**
     * Sets column id_erabiltzaile
     *
     * @param int $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setIdErabiltzaile($data)
    {
        $this->_IdErabiltzaile = $data;
        return $this;
    }

    /**
     * Gets column id_erabiltzaile
     *
     * @return int
     */
    public function getIdErabiltzaile()
    {
 
        return $this->_IdErabiltzaile;
    }


    /**
     * Sets column erabiltzailea
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setErabiltzailea($data)
    {
        $this->_Erabiltzailea = $data;
        return $this;
    }

    /**
     * Gets column erabiltzailea
     *
     * @return string
     */
    public function getErabiltzailea()
    {
 
        return $this->_Erabiltzailea;
    }


    /**
     * Sets column pasahitza
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setPasahitza($data)
    {
        $this->_Pasahitza = $data;
        return $this;
    }

    /**
     * Gets column pasahitza
     *
     * @return string
     */
    public function getPasahitza()
    {
 
        return $this->_Pasahitza;
    }


    /**
     * Sets column rekuperatu
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setRekuperatu($data)
    {
        $this->_Rekuperatu = $data;
        return $this;
    }

    /**
     * Gets column rekuperatu
     *
     * @return string
     */
    public function getRekuperatu()
    {
 
        return $this->_Rekuperatu;
    }


    /**
     * Sets column izena
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setIzena($data)
    {
        $this->_Izena = $data;
        return $this;
    }

    /**
     * Gets column izena
     *
     * @return string
     */
    public function getIzena()
    {
 
        return $this->_Izena;
    }


    /**
     * Sets column abizenak
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setAbizenak($data)
    {
        $this->_Abizenak = $data;
        return $this;
    }

    /**
     * Gets column abizenak
     *
     * @return string
     */
    public function getAbizenak()
    {
 
        return $this->_Abizenak;
    }


    /**
     * Sets column herria
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setHerria($data)
    {
        $this->_Herria = $data;
        return $this;
    }

    /**
     * Gets column herria
     *
     * @return string
     */
    public function getHerria()
    {
 
        return $this->_Herria;
    }


    /**
     * Sets column irudi_izena
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setIrudiIzena($data)
    {
        $this->_IrudiIzena = $data;
        return $this;
    }

    /**
     * Gets column irudi_izena
     *
     * @return string
     */
    public function getIrudiIzena()
    {
 
        return $this->_IrudiIzena;
    }


    /**
     * Sets column irudi_tamaina
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setIrudiTamaina($data)
    {
        $this->_IrudiTamaina = $data;
        return $this;
    }

    /**
     * Gets column irudi_tamaina
     *
     * @return string
     */
    public function getIrudiTamaina()
    {
 
        return $this->_IrudiTamaina;
    }


    /**
     * Sets column irudi_mota
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setIrudiMota($data)
    {
        $this->_IrudiMota = $data;
        return $this;
    }

    /**
     * Gets column irudi_mota
     *
     * @return string
     */
    public function getIrudiMota()
    {
 
        return $this->_IrudiMota;
    }


    /**
     * Sets column deskribapena
     *
     * @param text $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setDeskribapena($data)
    {
        $this->_Deskribapena = $data;
        return $this;
    }

    /**
     * Gets column deskribapena
     *
     * @return text
     */
    public function getDeskribapena()
    {
 
        return $this->_Deskribapena;
    }


    /**
     * Sets column posta
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setPosta($data)
    {
        $this->_Posta = $data;
        return $this;
    }

    /**
     * Gets column posta
     *
     * @return string
     */
    public function getPosta()
    {
 
        return $this->_Posta;
    }


    /**
     * Sets column facebook
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setFacebook($data)
    {
        $this->_Facebook = $data;
        return $this;
    }

    /**
     * Gets column facebook
     *
     * @return string
     */
    public function getFacebook()
    {
 
        return $this->_Facebook;
    }


    /**
     * Sets column twitter
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setTwitter($data)
    {
        $this->_Twitter = $data;
        return $this;
    }

    /**
     * Gets column twitter
     *
     * @return string
     */
    public function getTwitter()
    {
 
        return $this->_Twitter;
    }


    /**
     * Sets column jaiotze_data
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setJaiotzeData($data)
    {
        $this->_JaiotzeData = $data;
        return $this;
    }

    /**
     * Gets column jaiotze_data
     *
     * @return string
     */
    public function getJaiotzeData()
    {
 
        return $this->_JaiotzeData;
    }


    /**
     * Sets column url
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setUrl($data)
    {
        $this->_Url = $data;
        return $this;
    }

    /**
     * Gets column url
     *
     * @return string
     */
    public function getUrl()
    {
 
        return $this->_Url;
    }


    /**
     * Sets column alta. Stored in ISO 8601 format.
     *
     * @param string|Zend_Date $date
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setAlta($data)
    {
		if (! is_null($data) and ! $data instanceof Zend_Date) {

			$data = new Zend_Date($data, Zend_Date::ISO_8601, 'es_ES');
		}

        $this->_Alta = $data;
        return $this;
    }

    /**
     * Gets column alta
     *
     * @param boolean $returnZendDate
     * @return Zend_Date|null|string Zend_Date representation of this datetime if enabled, or ISO 8601 string if not
     */
    public function getAlta($returnZendDate = false)
    {

		if (is_null($this->_Alta)) {

			return null;
		}

        if ($returnZendDate) {

            return $this->_Alta;
        }

        return $this->_Alta->setTimezone(date_default_timezone_get())->toString('yyyy-MM-dd HH:mm:ss');
    }


    /**
     * Sets column aldaketa. Stored in ISO 8601 format.
     *
     * @param string|Zend_Date $date
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setAldaketa($data)
    {
		if (! is_null($data) and ! $data instanceof Zend_Date) {

			$data = new Zend_Date($data, Zend_Date::ISO_8601, 'es_ES');
		}

        $this->_Aldaketa = $data;
        return $this;
    }

    /**
     * Gets column aldaketa
     *
     * @param boolean $returnZendDate
     * @return Zend_Date|null|string Zend_Date representation of this datetime if enabled, or ISO 8601 string if not
     */
    public function getAldaketa($returnZendDate = false)
    {

		if (is_null($this->_Aldaketa)) {

			return null;
		}

        if ($returnZendDate) {

            return $this->_Aldaketa;
        }

        return $this->_Aldaketa->setTimezone(date_default_timezone_get())->toString('yyyy-MM-dd HH:mm:ss');
    }


    /**
     * Sets column giltza
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setGiltza($data)
    {
        $this->_Giltza = $data;
        return $this;
    }

    /**
     * Gets column giltza
     *
     * @return string
     */
    public function getGiltza()
    {
 
        return $this->_Giltza;
    }


    /**
     * Sets column aktibatua
     *
     * @param string $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setAktibatua($data)
    {
        $this->_Aktibatua = $data;
        return $this;
    }

    /**
     * Gets column aktibatua
     *
     * @return string
     */
    public function getAktibatua()
    {
 
        return $this->_Aktibatua;
    }

    /**
     * Sets dependent relations checks_ibfk_3
     *
     * @param array $data An array of Mintzatu_Model_Checks
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setChecks(array $data, $deleteOrphans = false)
    {
    	if ($deleteOrphans === true) {

			if ($this->_Checks === null) {

				$this->getChecks();
			}

			$oldContacts = $this->_Checks;

			if (is_array($oldContacts)) {

				$dataPKs = array();

				foreach ($data as $newItem) {

					if (is_numeric($pk = $newItem->getPrimaryKey())) {

						$dataPKs[] = $pk;
					}
				}
	
				foreach ($oldContacts as $oldItem) {

					if (! in_array($oldItem->getPrimaryKey(), $dataPKs)) {

						$this->_orphans[] = $oldItem;
					}	
				}
			}
    	}

        $this->_Checks = array();

        foreach ($data as $object) {
            $this->addChecks($object);
        }

        return $this;
    }

    /**
     * Sets dependent relations checks_ibfk_3
     *
     * @param Mintzatu_Model_Checks $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function addChecks(Mintzatu_Model_Checks $data)
    {
        $this->_Checks[] = $data;
        return $this;
    }

    /**
     * Gets dependent checks_ibfk_3
     *
     * @param boolean $load Load the object if it is not already
     * @return array The array of Mintzatu_Model_Checks
     */
    public function getChecks($load = true)
    {
        if ($this->_Checks === null && $load) {
            $this->getMapper()->loadRelated('ChecksIbfk3', $this);
        }

        return $this->_Checks;
    }

    /**
     * Sets dependent relations lekuak_ibfk_1
     *
     * @param array $data An array of Mintzatu_Model_Lekuak
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setLekuak(array $data, $deleteOrphans = false)
    {
    	if ($deleteOrphans === true) {

			if ($this->_Lekuak === null) {

				$this->getLekuak();
			}

			$oldContacts = $this->_Lekuak;

			if (is_array($oldContacts)) {

				$dataPKs = array();

				foreach ($data as $newItem) {

					if (is_numeric($pk = $newItem->getPrimaryKey())) {

						$dataPKs[] = $pk;
					}
				}
	
				foreach ($oldContacts as $oldItem) {

					if (! in_array($oldItem->getPrimaryKey(), $dataPKs)) {

						$this->_orphans[] = $oldItem;
					}	
				}
			}
    	}

        $this->_Lekuak = array();

        foreach ($data as $object) {
            $this->addLekuak($object);
        }

        return $this;
    }

    /**
     * Sets dependent relations lekuak_ibfk_1
     *
     * @param Mintzatu_Model_Lekuak $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function addLekuak(Mintzatu_Model_Lekuak $data)
    {
        $this->_Lekuak[] = $data;
        return $this;
    }

    /**
     * Gets dependent lekuak_ibfk_1
     *
     * @param boolean $load Load the object if it is not already
     * @return array The array of Mintzatu_Model_Lekuak
     */
    public function getLekuak($load = true)
    {
        if ($this->_Lekuak === null && $load) {
            $this->getMapper()->loadRelated('LekuakIbfk1', $this);
        }

        return $this->_Lekuak;
    }

    /**
     * Sets dependent relations rel_erabiltzaileak_ibfk_1
     *
     * @param array $data An array of Mintzatu_Model_RelErabiltzaileak
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setRelErabiltzaileakByIdErabiltzaile1(array $data, $deleteOrphans = false)
    {
    	if ($deleteOrphans === true) {

			if ($this->_RelErabiltzaileakByIdErabiltzaile1 === null) {

				$this->getRelErabiltzaileakByIdErabiltzaile1();
			}

			$oldContacts = $this->_RelErabiltzaileakByIdErabiltzaile1;

			if (is_array($oldContacts)) {

				$dataPKs = array();

				foreach ($data as $newItem) {

					if (is_numeric($pk = $newItem->getPrimaryKey())) {

						$dataPKs[] = $pk;
					}
				}
	
				foreach ($oldContacts as $oldItem) {

					if (! in_array($oldItem->getPrimaryKey(), $dataPKs)) {

						$this->_orphans[] = $oldItem;
					}	
				}
			}
    	}

        $this->_RelErabiltzaileakByIdErabiltzaile1 = array();

        foreach ($data as $object) {
            $this->addRelErabiltzaileakByIdErabiltzaile1($object);
        }

        return $this;
    }

    /**
     * Sets dependent relations rel_erabiltzaileak_ibfk_1
     *
     * @param Mintzatu_Model_RelErabiltzaileak $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function addRelErabiltzaileakByIdErabiltzaile1(Mintzatu_Model_RelErabiltzaileak $data)
    {
        $this->_RelErabiltzaileakByIdErabiltzaile1[] = $data;
        return $this;
    }

    /**
     * Gets dependent rel_erabiltzaileak_ibfk_1
     *
     * @param boolean $load Load the object if it is not already
     * @return array The array of Mintzatu_Model_RelErabiltzaileak
     */
    public function getRelErabiltzaileakByIdErabiltzaile1($load = true)
    {
        if ($this->_RelErabiltzaileakByIdErabiltzaile1 === null && $load) {
            $this->getMapper()->loadRelated('RelErabiltzaileakIbfk1', $this);
        }

        return $this->_RelErabiltzaileakByIdErabiltzaile1;
    }

    /**
     * Sets dependent relations rel_erabiltzaileak_ibfk_2
     *
     * @param array $data An array of Mintzatu_Model_RelErabiltzaileak
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function setRelErabiltzaileakByIdErabiltzaile2(array $data, $deleteOrphans = false)
    {
    	if ($deleteOrphans === true) {

			if ($this->_RelErabiltzaileakByIdErabiltzaile2 === null) {

				$this->getRelErabiltzaileakByIdErabiltzaile2();
			}

			$oldContacts = $this->_RelErabiltzaileakByIdErabiltzaile2;

			if (is_array($oldContacts)) {

				$dataPKs = array();

				foreach ($data as $newItem) {

					if (is_numeric($pk = $newItem->getPrimaryKey())) {

						$dataPKs[] = $pk;
					}
				}
	
				foreach ($oldContacts as $oldItem) {

					if (! in_array($oldItem->getPrimaryKey(), $dataPKs)) {

						$this->_orphans[] = $oldItem;
					}	
				}
			}
    	}

        $this->_RelErabiltzaileakByIdErabiltzaile2 = array();

        foreach ($data as $object) {
            $this->addRelErabiltzaileakByIdErabiltzaile2($object);
        }

        return $this;
    }

    /**
     * Sets dependent relations rel_erabiltzaileak_ibfk_2
     *
     * @param Mintzatu_Model_RelErabiltzaileak $data
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function addRelErabiltzaileakByIdErabiltzaile2(Mintzatu_Model_RelErabiltzaileak $data)
    {
        $this->_RelErabiltzaileakByIdErabiltzaile2[] = $data;
        return $this;
    }

    /**
     * Gets dependent rel_erabiltzaileak_ibfk_2
     *
     * @param boolean $load Load the object if it is not already
     * @return array The array of Mintzatu_Model_RelErabiltzaileak
     */
    public function getRelErabiltzaileakByIdErabiltzaile2($load = true)
    {
        if ($this->_RelErabiltzaileakByIdErabiltzaile2 === null && $load) {
            $this->getMapper()->loadRelated('RelErabiltzaileakIbfk2', $this);
        }

        return $this->_RelErabiltzaileakByIdErabiltzaile2;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\Erabiltzaileak
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\Erabiltzaileak')) {

                $this->setMapper(new \Mappers\Sql\Erabiltzaileak);

            } else if (class_exists('\Mappers\Soap\Erabiltzaileak')) {

                $this->setMapper(new \Mappers\Soap\Erabiltzaileak);

            } else {

                Throw new \Exception("Not a valid mapper class found");
            }

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(false);
        }

        return $this->_mapper;
    }

    /**
     * Returns the validator class for this model
     *
     * @return null | Mintzatu_Model_Validator_Erabiltzaileak
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_Erabiltzaileak')) {
            
                $this->setValidator(new Mintzatu_Validator_Erabiltzaileak);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\Erabiltzaileak::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdErabiltzaile() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_erabiltzaile = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdErabiltzaile()));
    }
}
