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
class Mintzatu_Model_Raw_Checks extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdCheck;

    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdErabiltzaile;

    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdLekua;

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
    protected $_Noiz;

    /**
     * Database var type enum('web','app')
     *
     * @var string
     */
    protected $_Nondik;


    /**
     * Parent relation checks_ibfk_2
     *
     * @var Mintzatu_Model_Lekuak
     */
    protected $_Lekuak;

    /**
     * Parent relation checks_ibfk_3
     *
     * @var Mintzatu_Model_Erabiltzaileak
     */
    protected $_Erabiltzaileak;


    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'id_check'=>'IdCheck',
            'id_erabiltzaile'=>'IdErabiltzaile',
            'id_lekua'=>'IdLekua',
            'iruzkina'=>'Iruzkina',
            'noiz'=>'Noiz',
            'nondik'=>'Nondik',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('check','erabiltzaile','lekua'));

        $this->setParentList(array(
            'ChecksIbfk2'=> array(
                    'property' => 'Lekuak',
                    'table_name' => 'Lekuak',
                ),
            'ChecksIbfk3'=> array(
                    'property' => 'Erabiltzaileak',
                    'table_name' => 'Erabiltzaileak',
                ),
        ));

        $this->setDependentList(array(
        ));

		parent::__construct();
    }




    /**
     * Sets column id_check
     *
     * @param int $data
     * @return Mintzatu_Model_Checks
     */
    public function setIdCheck($data)
    {
        $this->_IdCheck = $data;
        return $this;
    }

    /**
     * Gets column id_check
     *
     * @return int
     */
    public function getIdCheck()
    {
 
        return $this->_IdCheck;
    }


    /**
     * Sets column id_erabiltzaile
     *
     * @param int $data
     * @return Mintzatu_Model_Checks
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
     * Sets column id_lekua
     *
     * @param int $data
     * @return Mintzatu_Model_Checks
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
     * Sets column iruzkina
     *
     * @param text $data
     * @return Mintzatu_Model_Checks
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
     * Sets column noiz
     *
     * @param string $data
     * @return Mintzatu_Model_Checks
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
     * Sets column nondik
     *
     * @param string $data
     * @return Mintzatu_Model_Checks
     */
    public function setNondik($data)
    {
        $this->_Nondik = $data;
        return $this;
    }

    /**
     * Gets column nondik
     *
     * @return string
     */
    public function getNondik()
    {
 
        return $this->_Nondik;
    }

    /**
     * Sets parent relation IdLekua
     *
     * @param Mintzatu_Model_Lekuak $data
     * @return Mintzatu_Model_Checks
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
            $this->getMapper()->loadRelated('ChecksIbfk2', $this);
        }

        return $this->_Lekuak;
    }

    /**
     * Sets parent relation IdErabiltzaile
     *
     * @param Mintzatu_Model_Erabiltzaileak $data
     * @return Mintzatu_Model_Checks
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
            $this->getMapper()->loadRelated('ChecksIbfk3', $this);
        }

        return $this->_Erabiltzaileak;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\Checks
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\Checks')) {

                $this->setMapper(new \Mappers\Sql\Checks);

            } else if (class_exists('\Mappers\Soap\Checks')) {

                $this->setMapper(new \Mappers\Soap\Checks);

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
     * @return null | Mintzatu_Model_Validator_Checks
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_Checks')) {
            
                $this->setValidator(new Mintzatu_Validator_Checks);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\Checks::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdCheck() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_check = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdCheck()));
    }
}
