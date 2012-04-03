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
class Mintzatu_Model_Raw_Lekuak extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdLekua;

    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdKategoria;

    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdErabiltzaile;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Izena;

    /**
     * Database var type varchar(350)
     *
     * @var string
     */
    protected $_Helbidea;

    /**
     * Database var type int(10)
     *
     * @var int
     */
    protected $_Postakodea;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Herria;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Probintzia;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Estatua;

    /**
     * Database var type varchar(500)
     *
     * @var string
     */
    protected $_Mapa;

    /**
     * Database var type float
     *
     * @var float
     */
    protected $_Latitudea;

    /**
     * Database var type float
     *
     * @var float
     */
    protected $_Longitudea;

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
    protected $_Url;


    /**
     * Parent relation lekuak_ibfk_1
     *
     * @var Mintzatu_Model_Erabiltzaileak
     */
    protected $_Erabiltzaileak;

    /**
     * Parent relation lekuak_ibfk_2
     *
     * @var Mintzatu_Model_Kategoriak
     */
    protected $_Kategoriak;


    /**
     * Dependent relation aktibitatea_ibfk_1
     * Type: One-to-Many relationship
     *
     * @var Mintzatu_Model_Aktibitatea[]
     */
    protected $_Aktibitatea;

    /**
     * Dependent relation checks_ibfk_2
     * Type: One-to-Many relationship
     *
     * @var Mintzatu_Model_Checks[]
     */
    protected $_Checks;

    /**
     * Dependent relation lekuen_irudiak_ibfk_1
     * Type: One-to-Many relationship
     *
     * @var Mintzatu_Model_LekuenIrudiak[]
     */
    protected $_LekuenIrudiak;

    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'id_lekua'=>'IdLekua',
            'id_kategoria'=>'IdKategoria',
            'id_erabiltzaile'=>'IdErabiltzaile',
            'izena'=>'Izena',
            'helbidea'=>'Helbidea',
            'postakodea'=>'Postakodea',
            'herria'=>'Herria',
            'probintzia'=>'Probintzia',
            'estatua'=>'Estatua',
            'mapa'=>'Mapa',
            'latitudea'=>'Latitudea',
            'longitudea'=>'Longitudea',
            'deskribapena'=>'Deskribapena',
            'url'=>'Url',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('lekua','kategoria','erabiltzaile'));

        $this->setParentList(array(
            'LekuakIbfk1'=> array(
                    'property' => 'Erabiltzaileak',
                    'table_name' => 'Erabiltzaileak',
                ),
            'LekuakIbfk2'=> array(
                    'property' => 'Kategoriak',
                    'table_name' => 'Kategoriak',
                ),
        ));

        $this->setDependentList(array(
            'AktibitateaIbfk1' => array(
                    'property' => 'Aktibitatea',
                    'table_name' => 'Aktibitatea',
                ),
            'ChecksIbfk2' => array(
                    'property' => 'Checks',
                    'table_name' => 'Checks',
                ),
            'LekuenIrudiakIbfk1' => array(
                    'property' => 'LekuenIrudiak',
                    'table_name' => 'LekuenIrudiak',
                ),
        ));
 
        $this->setOnDeleteCascadeRelationships(array(
        	'aktibitatea_ibfk_1',
        	'checks_ibfk_2',
        	'lekuen_irudiak_ibfk_1'
        ));

		parent::__construct();
    }




    /**
     * Sets column id_lekua
     *
     * @param int $data
     * @return Mintzatu_Model_Lekuak
     */
    public function setIdLekua($data)
    {
        $this->_IdLekua = $data;
        return $this;
    }

    /**
     * Gets column id_lekua
     *
     * @return int
     */
    public function getIdLekua()
    {
 
        return $this->_IdLekua;
    }


    /**
     * Sets column id_kategoria
     *
     * @param int $data
     * @return Mintzatu_Model_Lekuak
     */
    public function setIdKategoria($data)
    {
        $this->_IdKategoria = $data;
        return $this;
    }

    /**
     * Gets column id_kategoria
     *
     * @return int
     */
    public function getIdKategoria()
    {
 
        return $this->_IdKategoria;
    }


    /**
     * Sets column id_erabiltzaile
     *
     * @param int $data
     * @return Mintzatu_Model_Lekuak
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
     * Sets column izena
     *
     * @param string $data
     * @return Mintzatu_Model_Lekuak
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
     * Sets column helbidea
     *
     * @param string $data
     * @return Mintzatu_Model_Lekuak
     */
    public function setHelbidea($data)
    {
        $this->_Helbidea = $data;
        return $this;
    }

    /**
     * Gets column helbidea
     *
     * @return string
     */
    public function getHelbidea()
    {
 
        return $this->_Helbidea;
    }


    /**
     * Sets column postakodea
     *
     * @param int $data
     * @return Mintzatu_Model_Lekuak
     */
    public function setPostakodea($data)
    {
        $this->_Postakodea = $data;
        return $this;
    }

    /**
     * Gets column postakodea
     *
     * @return int
     */
    public function getPostakodea()
    {
 
        return $this->_Postakodea;
    }


    /**
     * Sets column herria
     *
     * @param string $data
     * @return Mintzatu_Model_Lekuak
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
     * Sets column probintzia
     *
     * @param string $data
     * @return Mintzatu_Model_Lekuak
     */
    public function setProbintzia($data)
    {
        $this->_Probintzia = $data;
        return $this;
    }

    /**
     * Gets column probintzia
     *
     * @return string
     */
    public function getProbintzia()
    {
 
        return $this->_Probintzia;
    }


    /**
     * Sets column estatua
     *
     * @param string $data
     * @return Mintzatu_Model_Lekuak
     */
    public function setEstatua($data)
    {
        $this->_Estatua = $data;
        return $this;
    }

    /**
     * Gets column estatua
     *
     * @return string
     */
    public function getEstatua()
    {
 
        return $this->_Estatua;
    }


    /**
     * Sets column mapa
     *
     * @param string $data
     * @return Mintzatu_Model_Lekuak
     */
    public function setMapa($data)
    {
        $this->_Mapa = $data;
        return $this;
    }

    /**
     * Gets column mapa
     *
     * @return string
     */
    public function getMapa()
    {
 
        return $this->_Mapa;
    }


    /**
     * Sets column latitudea
     *
     * @param float $data
     * @return Mintzatu_Model_Lekuak
     */
    public function setLatitudea($data)
    {
        $this->_Latitudea = $data;
        return $this;
    }

    /**
     * Gets column latitudea
     *
     * @return float
     */
    public function getLatitudea()
    {
 
        return $this->_Latitudea;
    }


    /**
     * Sets column longitudea
     *
     * @param float $data
     * @return Mintzatu_Model_Lekuak
     */
    public function setLongitudea($data)
    {
        $this->_Longitudea = $data;
        return $this;
    }

    /**
     * Gets column longitudea
     *
     * @return float
     */
    public function getLongitudea()
    {
 
        return $this->_Longitudea;
    }


    /**
     * Sets column deskribapena
     *
     * @param text $data
     * @return Mintzatu_Model_Lekuak
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
     * Sets column url
     *
     * @param string $data
     * @return Mintzatu_Model_Lekuak
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
     * Sets parent relation IdErabiltzaile
     *
     * @param Mintzatu_Model_Erabiltzaileak $data
     * @return Mintzatu_Model_Lekuak
     */
    public function setErabiltzaileak(Mintzatu_Model_Erabiltzaileak $data)
    {
        $this->_Erabiltzaileak = $data;

        $primary_key = $data->getPrimaryKey();
        if (is_array($primary_key)) {
            $primary_key = $primary_key['id_erabiltzaile'];
        }

        $this->setIdErabiltzaile($primary_key);

        return $this;
    }

    /**
     * Gets parent IdErabiltzaile
     *
     * @param boolean $load Load the object if it is not already
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function getErabiltzaileak($load = true)
    {
        if ($this->_Erabiltzaileak === null && $load) {
            $this->getMapper()->loadRelated('LekuakIbfk1', $this);
        }

        return $this->_Erabiltzaileak;
    }

    /**
     * Sets parent relation IdKategoria
     *
     * @param Mintzatu_Model_Kategoriak $data
     * @return Mintzatu_Model_Lekuak
     */
    public function setKategoriak(Mintzatu_Model_Kategoriak $data)
    {
        $this->_Kategoriak = $data;

        $primary_key = $data->getPrimaryKey();
        if (is_array($primary_key)) {
            $primary_key = $primary_key['id_kategoria'];
        }

        $this->setIdKategoria($primary_key);

        return $this;
    }

    /**
     * Gets parent IdKategoria
     *
     * @param boolean $load Load the object if it is not already
     * @return Mintzatu_Model_Kategoriak
     */
    public function getKategoriak($load = true)
    {
        if ($this->_Kategoriak === null && $load) {
            $this->getMapper()->loadRelated('LekuakIbfk2', $this);
        }

        return $this->_Kategoriak;
    }

    /**
     * Sets dependent relations aktibitatea_ibfk_1
     *
     * @param array $data An array of Mintzatu_Model_Aktibitatea
     * @return Mintzatu_Model_Lekuak
     */
    public function setAktibitatea(array $data, $deleteOrphans = false)
    {
    	if ($deleteOrphans === true) {

			if ($this->_Aktibitatea === null) {

				$this->getAktibitatea();
			}

			$oldContacts = $this->_Aktibitatea;

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

        $this->_Aktibitatea = array();

        foreach ($data as $object) {
            $this->addAktibitatea($object);
        }

        return $this;
    }

    /**
     * Sets dependent relations aktibitatea_ibfk_1
     *
     * @param Mintzatu_Model_Aktibitatea $data
     * @return Mintzatu_Model_Lekuak
     */
    public function addAktibitatea(Mintzatu_Model_Aktibitatea $data)
    {
        $this->_Aktibitatea[] = $data;
        return $this;
    }

    /**
     * Gets dependent aktibitatea_ibfk_1
     *
     * @param boolean $load Load the object if it is not already
     * @return array The array of Mintzatu_Model_Aktibitatea
     */
    public function getAktibitatea($load = true)
    {
        if ($this->_Aktibitatea === null && $load) {
            $this->getMapper()->loadRelated('AktibitateaIbfk1', $this);
        }

        return $this->_Aktibitatea;
    }

    /**
     * Sets dependent relations checks_ibfk_2
     *
     * @param array $data An array of Mintzatu_Model_Checks
     * @return Mintzatu_Model_Lekuak
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
     * Sets dependent relations checks_ibfk_2
     *
     * @param Mintzatu_Model_Checks $data
     * @return Mintzatu_Model_Lekuak
     */
    public function addChecks(Mintzatu_Model_Checks $data)
    {
        $this->_Checks[] = $data;
        return $this;
    }

    /**
     * Gets dependent checks_ibfk_2
     *
     * @param boolean $load Load the object if it is not already
     * @return array The array of Mintzatu_Model_Checks
     */
    public function getChecks($load = true)
    {
        if ($this->_Checks === null && $load) {
            $this->getMapper()->loadRelated('ChecksIbfk2', $this);
        }

        return $this->_Checks;
    }

    /**
     * Sets dependent relations lekuen_irudiak_ibfk_1
     *
     * @param array $data An array of Mintzatu_Model_LekuenIrudiak
     * @return Mintzatu_Model_Lekuak
     */
    public function setLekuenIrudiak(array $data, $deleteOrphans = false)
    {
    	if ($deleteOrphans === true) {

			if ($this->_LekuenIrudiak === null) {

				$this->getLekuenIrudiak();
			}

			$oldContacts = $this->_LekuenIrudiak;

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

        $this->_LekuenIrudiak = array();

        foreach ($data as $object) {
            $this->addLekuenIrudiak($object);
        }

        return $this;
    }

    /**
     * Sets dependent relations lekuen_irudiak_ibfk_1
     *
     * @param Mintzatu_Model_LekuenIrudiak $data
     * @return Mintzatu_Model_Lekuak
     */
    public function addLekuenIrudiak(Mintzatu_Model_LekuenIrudiak $data)
    {
        $this->_LekuenIrudiak[] = $data;
        return $this;
    }

    /**
     * Gets dependent lekuen_irudiak_ibfk_1
     *
     * @param boolean $load Load the object if it is not already
     * @return array The array of Mintzatu_Model_LekuenIrudiak
     */
    public function getLekuenIrudiak($load = true)
    {
        if ($this->_LekuenIrudiak === null && $load) {
            $this->getMapper()->loadRelated('LekuenIrudiakIbfk1', $this);
        }

        return $this->_LekuenIrudiak;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\Lekuak
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\Lekuak')) {

                $this->setMapper(new \Mappers\Sql\Lekuak);

            } else if (class_exists('\Mappers\Soap\Lekuak')) {

                $this->setMapper(new \Mappers\Soap\Lekuak);

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
     * @return null | Mintzatu_Model_Validator_Lekuak
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_Lekuak')) {
            
                $this->setValidator(new Mintzatu_Validator_Lekuak);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\Lekuak::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdLekua() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_lekua = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdLekua()));
    }
}
