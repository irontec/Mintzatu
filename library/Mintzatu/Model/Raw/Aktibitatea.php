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
class Mintzatu_Model_Raw_Aktibitatea extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdAktibitatea;

    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdLerroa;

    /**
     * Database var type varchar(250)
     *
     * @var string
     */
    protected $_Taula;

    /**
     * Database var type enum('erabiltzaileberria','lekuberria','checkina')
     *
     * @var string
     */
    protected $_Akzioa;

    /**
     * Database var type timestamp
     *
     * @var string
     */
    protected $_Noiz;


    /**
     * Parent relation aktibitatea_ibfk_1
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
            'id_aktibitatea'=>'IdAktibitatea',
            'id_lerroa'=>'IdLerroa',
            'taula'=>'Taula',
            'akzioa'=>'Akzioa',
            'noiz'=>'Noiz',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('aktibitatea','lerroa'));

        $this->setParentList(array(
            'AktibitateaIbfk1'=> array(
                    'property' => 'Lekuak',
                    'table_name' => 'Lekuak',
                ),
        ));

        $this->setDependentList(array(
        ));

		parent::__construct();
    }




    /**
     * Sets column id_aktibitatea
     *
     * @param int $data
     * @return Mintzatu_Model_Aktibitatea
     */
    public function setIdAktibitatea($data)
    {
        $this->_IdAktibitatea = $data;
        return $this;
    }

    /**
     * Gets column id_aktibitatea
     *
     * @return int
     */
    public function getIdAktibitatea()
    {
 
        return $this->_IdAktibitatea;
    }


    /**
     * Sets column id_lerroa
     *
     * @param int $data
     * @return Mintzatu_Model_Aktibitatea
     */
    public function setIdLerroa($data)
    {
        $this->_IdLerroa = $data;
        return $this;
    }

    /**
     * Gets column id_lerroa
     *
     * @return int
     */
    public function getIdLerroa()
    {
 
        return $this->_IdLerroa;
    }


    /**
     * Sets column taula
     *
     * @param string $data
     * @return Mintzatu_Model_Aktibitatea
     */
    public function setTaula($data)
    {
        $this->_Taula = $data;
        return $this;
    }

    /**
     * Gets column taula
     *
     * @return string
     */
    public function getTaula()
    {
 
        return $this->_Taula;
    }


    /**
     * Sets column akzioa
     *
     * @param string $data
     * @return Mintzatu_Model_Aktibitatea
     */
    public function setAkzioa($data)
    {
        $this->_Akzioa = $data;
        return $this;
    }

    /**
     * Gets column akzioa
     *
     * @return string
     */
    public function getAkzioa()
    {
 
        return $this->_Akzioa;
    }


    /**
     * Sets column noiz
     *
     * @param string $data
     * @return Mintzatu_Model_Aktibitatea
     */
    public function setNoiz($data)
    {
        $this->_Noiz = $data;
        return $this;
    }

    /**
     * Gets column noiz
     *
     * @return string
     */
    public function getNoiz()
    {
 
        return $this->_Noiz;
    }

    /**
     * Sets parent relation IdLerroa
     *
     * @param Mintzatu_Model_Lekuak $data
     * @return Mintzatu_Model_Aktibitatea
     */
    public function setLekuak(Mintzatu_Model_Lekuak $data)
    {
        $this->_Lekuak = $data;

        $primary_key = $data->getPrimaryKey();
        if (is_array($primary_key)) {
            $primary_key = $primary_key['id_lekua'];
        }

        $this->setIdLerroa($primary_key);

        return $this;
    }

    /**
     * Gets parent IdLerroa
     *
     * @param boolean $load Load the object if it is not already
     * @return Mintzatu_Model_Lekuak
     */
    public function getLekuak($load = true)
    {
        if ($this->_Lekuak === null && $load) {
            $this->getMapper()->loadRelated('AktibitateaIbfk1', $this);
        }

        return $this->_Lekuak;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\Aktibitatea
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\Aktibitatea')) {

                $this->setMapper(new \Mappers\Sql\Aktibitatea);

            } else if (class_exists('\Mappers\Soap\Aktibitatea')) {

                $this->setMapper(new \Mappers\Soap\Aktibitatea);

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
     * @return null | Mintzatu_Model_Validator_Aktibitatea
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_Aktibitatea')) {
            
                $this->setValidator(new Mintzatu_Validator_Aktibitatea);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\Aktibitatea::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdAktibitatea() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_aktibitatea = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdAktibitatea()));
    }
}
