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
class Mintzatu_Model_Raw_Kategoriak extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdKategoria;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Izena;

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
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_IrudiIzena;

    /**
     * Database var type int(50)
     *
     * @var int
     */
    protected $_IrudiTamaina;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_IrudiMota;



    /**
     * Dependent relation lekuak_ibfk_2
     * Type: One-to-Many relationship
     *
     * @var Mintzatu_Model_Lekuak[]
     */
    protected $_Lekuak;

    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'id_kategoria'=>'IdKategoria',
            'izena'=>'Izena',
            'deskribapena'=>'Deskribapena',
            'url'=>'Url',
            'irudi_izena'=>'IrudiIzena',
            'irudi_tamaina'=>'IrudiTamaina',
            'irudi_mota'=>'IrudiMota',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('kategoria','izena','tamaina','mota'));

        $this->setParentList(array(
        ));

        $this->setDependentList(array(
            'LekuakIbfk2' => array(
                    'property' => 'Lekuak',
                    'table_name' => 'Lekuak',
                ),
        ));
 
        $this->setOnDeleteSetNullRelationships(array(
			'lekuak_ibfk_2'
		));

		parent::__construct();
    }




    /**
     * Sets column id_kategoria
     *
     * @param int $data
     * @return Mintzatu_Model_Kategoriak
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
     * Sets column izena
     *
     * @param string $data
     * @return Mintzatu_Model_Kategoriak
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
     * Sets column deskribapena
     *
     * @param text $data
     * @return Mintzatu_Model_Kategoriak
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
     * @return Mintzatu_Model_Kategoriak
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
     * Sets column irudi_izena
     *
     * @param string $data
     * @return Mintzatu_Model_Kategoriak
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
     * @param int $data
     * @return Mintzatu_Model_Kategoriak
     */
    public function setIrudiTamaina($data)
    {
        $this->_IrudiTamaina = $data;
        return $this;
    }

    /**
     * Gets column irudi_tamaina
     *
     * @return int
     */
    public function getIrudiTamaina()
    {
 
        return $this->_IrudiTamaina;
    }


    /**
     * Sets column irudi_mota
     *
     * @param string $data
     * @return Mintzatu_Model_Kategoriak
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
     * Sets dependent relations lekuak_ibfk_2
     *
     * @param array $data An array of Mintzatu_Model_Lekuak
     * @return Mintzatu_Model_Kategoriak
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
     * Sets dependent relations lekuak_ibfk_2
     *
     * @param Mintzatu_Model_Lekuak $data
     * @return Mintzatu_Model_Kategoriak
     */
    public function addLekuak(Mintzatu_Model_Lekuak $data)
    {
        $this->_Lekuak[] = $data;
        return $this;
    }

    /**
     * Gets dependent lekuak_ibfk_2
     *
     * @param boolean $load Load the object if it is not already
     * @return array The array of Mintzatu_Model_Lekuak
     */
    public function getLekuak($load = true)
    {
        if ($this->_Lekuak === null && $load) {
            $this->getMapper()->loadRelated('LekuakIbfk2', $this);
        }

        return $this->_Lekuak;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\Kategoriak
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\Kategoriak')) {

                $this->setMapper(new \Mappers\Sql\Kategoriak);

            } else if (class_exists('\Mappers\Soap\Kategoriak')) {

                $this->setMapper(new \Mappers\Soap\Kategoriak);

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
     * @return null | Mintzatu_Model_Validator_Kategoriak
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_Kategoriak')) {
            
                $this->setValidator(new Mintzatu_Validator_Kategoriak);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\Kategoriak::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdKategoria() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_kategoria = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdKategoria()));
    }
}
