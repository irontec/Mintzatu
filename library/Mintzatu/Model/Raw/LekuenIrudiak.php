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
class Mintzatu_Model_Raw_LekuenIrudiak extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdIrudia;

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
    protected $_IdErabiltzailea;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_IrudiIzena;

    /**
     * Database var type varchar(50)
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
    protected $_Iruzkina;

    /**
     * Database var type timestamp
     *
     * @var string
     */
    protected $_Datetime;


    /**
     * Parent relation lekuen_irudiak_ibfk_1
     *
     * @var Mintzatu_Model_Lekuak
     */
    protected $_Lekuak;


    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'id_irudia'=>'IdIrudia',
            'id_lekua'=>'IdLekua',
            'id_erabiltzailea'=>'IdErabiltzailea',
            'irudi_izena'=>'IrudiIzena',
            'irudi_tamaina'=>'IrudiTamaina',
            'irudi_mota'=>'IrudiMota',
            'iruzkina'=>'Iruzkina',
            'datetime'=>'Datetime',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('irudia','lekua','erabiltzailea','izena','tamaina','mota'));

        $this->setParentList(array(
            'LekuenIrudiakIbfk1'=> array(
                    'property' => 'Lekuak',
                    'table_name' => 'Lekuak',
                ),
        ));

        $this->setDependentList(array(
        ));

		parent::__construct();
    }




    /**
     * Sets column id_irudia
     *
     * @param int $data
     * @return Mintzatu_Model_LekuenIrudiak
     */
    public function setIdIrudia($data)
    {
        $this->_IdIrudia = $data;
        return $this;
    }

    /**
     * Gets column id_irudia
     *
     * @return int
     */
    public function getIdIrudia()
    {
 
        return $this->_IdIrudia;
    }


    /**
     * Sets column id_lekua
     *
     * @param int $data
     * @return Mintzatu_Model_LekuenIrudiak
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
     * Sets column id_erabiltzailea
     *
     * @param int $data
     * @return Mintzatu_Model_LekuenIrudiak
     */
    public function setIdErabiltzailea($data)
    {
        $this->_IdErabiltzailea = $data;
        return $this;
    }

    /**
     * Gets column id_erabiltzailea
     *
     * @return int
     */
    public function getIdErabiltzailea()
    {
 
        return $this->_IdErabiltzailea;
    }


    /**
     * Sets column irudi_izena
     *
     * @param string $data
     * @return Mintzatu_Model_LekuenIrudiak
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
     * @return Mintzatu_Model_LekuenIrudiak
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
     * @return Mintzatu_Model_LekuenIrudiak
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
     * Sets column iruzkina
     *
     * @param text $data
     * @return Mintzatu_Model_LekuenIrudiak
     */
    public function setIruzkina($data)
    {
        $this->_Iruzkina = $data;
        return $this;
    }

    /**
     * Gets column iruzkina
     *
     * @return text
     */
    public function getIruzkina()
    {
 
        return $this->_Iruzkina;
    }


    /**
     * Sets column datetime
     *
     * @param string $data
     * @return Mintzatu_Model_LekuenIrudiak
     */
    public function setDatetime($data)
    {
        $this->_Datetime = $data;
        return $this;
    }

    /**
     * Gets column datetime
     *
     * @return string
     */
    public function getDatetime()
    {
 
        return $this->_Datetime;
    }

    /**
     * Sets parent relation IdLekua
     *
     * @param Mintzatu_Model_Lekuak $data
     * @return Mintzatu_Model_LekuenIrudiak
     */
    public function setLekuak(Mintzatu_Model_Lekuak $data)
    {
        $this->_Lekuak = $data;

        $primary_key = $data->getPrimaryKey();
        if (is_array($primary_key)) {
            $primary_key = $primary_key['id_lekua'];
        }

        $this->setIdLekua($primary_key);

        return $this;
    }

    /**
     * Gets parent IdLekua
     *
     * @param boolean $load Load the object if it is not already
     * @return Mintzatu_Model_Lekuak
     */
    public function getLekuak($load = true)
    {
        if ($this->_Lekuak === null && $load) {
            $this->getMapper()->loadRelated('LekuenIrudiakIbfk1', $this);
        }

        return $this->_Lekuak;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\LekuenIrudiak
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\LekuenIrudiak')) {

                $this->setMapper(new \Mappers\Sql\LekuenIrudiak);

            } else if (class_exists('\Mappers\Soap\LekuenIrudiak')) {

                $this->setMapper(new \Mappers\Soap\LekuenIrudiak);

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
     * @return null | Mintzatu_Model_Validator_LekuenIrudiak
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_LekuenIrudiak')) {
            
                $this->setValidator(new Mintzatu_Validator_LekuenIrudiak);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\LekuenIrudiak::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdIrudia() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_irudia = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdIrudia()));
    }
}
