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
class Mintzatu_Model_Raw_RelErabiltzaileak extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdRel;

    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdErabiltzaile1;

    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdErabiltzaile2;

    /**
     * Database var type timestamp
     *
     * @var string
     */
    protected $_Noiz;

    /**
     * Database var type enum('0','1','2')
     *
     * @var string
     */
    protected $_Lagunak;


    /**
     * Parent relation rel_erabiltzaileak_ibfk_1
     *
     * @var Mintzatu_Model_Erabiltzaileak
     */
    protected $_ErabiltzaileakByIdErabiltzaile1;

    /**
     * Parent relation rel_erabiltzaileak_ibfk_2
     *
     * @var Mintzatu_Model_Erabiltzaileak
     */
    protected $_ErabiltzaileakByIdErabiltzaile2;


    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'id_rel'=>'IdRel',
            'id_erabiltzaile1'=>'IdErabiltzaile1',
            'id_erabiltzaile2'=>'IdErabiltzaile2',
            'noiz'=>'Noiz',
            'lagunak'=>'Lagunak',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('rel','erabiltzaile1','erabiltzaile2'));

        $this->setParentList(array(
            'RelErabiltzaileakIbfk1'=> array(
                    'property' => 'ErabiltzaileakByIdErabiltzaile1',
                    'table_name' => 'Erabiltzaileak',
                ),
            'RelErabiltzaileakIbfk2'=> array(
                    'property' => 'ErabiltzaileakByIdErabiltzaile2',
                    'table_name' => 'Erabiltzaileak',
                ),
        ));

        $this->setDependentList(array(
        ));

		parent::__construct();
    }




    /**
     * Sets column id_rel
     *
     * @param int $data
     * @return Mintzatu_Model_RelErabiltzaileak
     */
    public function setIdRel($data)
    {
        $this->_IdRel = $data;
        return $this;
    }

    /**
     * Gets column id_rel
     *
     * @return int
     */
    public function getIdRel()
    {
 
        return $this->_IdRel;
    }


    /**
     * Sets column id_erabiltzaile1
     *
     * @param int $data
     * @return Mintzatu_Model_RelErabiltzaileak
     */
    public function setIdErabiltzaile1($data)
    {
        $this->_IdErabiltzaile1 = $data;
        return $this;
    }

    /**
     * Gets column id_erabiltzaile1
     *
     * @return int
     */
    public function getIdErabiltzaile1()
    {
 
        return $this->_IdErabiltzaile1;
    }


    /**
     * Sets column id_erabiltzaile2
     *
     * @param int $data
     * @return Mintzatu_Model_RelErabiltzaileak
     */
    public function setIdErabiltzaile2($data)
    {
        $this->_IdErabiltzaile2 = $data;
        return $this;
    }

    /**
     * Gets column id_erabiltzaile2
     *
     * @return int
     */
    public function getIdErabiltzaile2()
    {
 
        return $this->_IdErabiltzaile2;
    }


    /**
     * Sets column noiz
     *
     * @param string $data
     * @return Mintzatu_Model_RelErabiltzaileak
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
     * Sets column lagunak
     *
     * @param string $data
     * @return Mintzatu_Model_RelErabiltzaileak
     */
    public function setLagunak($data)
    {
        $this->_Lagunak = $data;
        return $this;
    }

    /**
     * Gets column lagunak
     *
     * @return string
     */
    public function getLagunak()
    {
 
        return $this->_Lagunak;
    }

    /**
     * Sets parent relation IdErabiltzaile1
     *
     * @param Mintzatu_Model_Erabiltzaileak $data
     * @return Mintzatu_Model_RelErabiltzaileak
     */
    public function setErabiltzaileakByIdErabiltzaile1(Mintzatu_Model_Erabiltzaileak $data)
    {
        $this->_ErabiltzaileakByIdErabiltzaile1 = $data;

        $primary_key = $data->getPrimaryKey();
        if (is_array($primary_key)) {
            $primary_key = $primary_key['id_erabiltzaile'];
        }

        $this->setIdErabiltzaile1($primary_key);

        return $this;
    }

    /**
     * Gets parent IdErabiltzaile1
     *
     * @param boolean $load Load the object if it is not already
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function getErabiltzaileakByIdErabiltzaile1($load = true)
    {
        if ($this->_ErabiltzaileakByIdErabiltzaile1 === null && $load) {
            $this->getMapper()->loadRelated('RelErabiltzaileakIbfk1', $this);
        }

        return $this->_ErabiltzaileakByIdErabiltzaile1;
    }

    /**
     * Sets parent relation IdErabiltzaile2
     *
     * @param Mintzatu_Model_Erabiltzaileak $data
     * @return Mintzatu_Model_RelErabiltzaileak
     */
    public function setErabiltzaileakByIdErabiltzaile2(Mintzatu_Model_Erabiltzaileak $data)
    {
        $this->_ErabiltzaileakByIdErabiltzaile2 = $data;

        $primary_key = $data->getPrimaryKey();
        if (is_array($primary_key)) {
            $primary_key = $primary_key['id_erabiltzaile'];
        }

        $this->setIdErabiltzaile2($primary_key);

        return $this;
    }

    /**
     * Gets parent IdErabiltzaile2
     *
     * @param boolean $load Load the object if it is not already
     * @return Mintzatu_Model_Erabiltzaileak
     */
    public function getErabiltzaileakByIdErabiltzaile2($load = true)
    {
        if ($this->_ErabiltzaileakByIdErabiltzaile2 === null && $load) {
            $this->getMapper()->loadRelated('RelErabiltzaileakIbfk2', $this);
        }

        return $this->_ErabiltzaileakByIdErabiltzaile2;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\RelErabiltzaileak
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\RelErabiltzaileak')) {

                $this->setMapper(new \Mappers\Sql\RelErabiltzaileak);

            } else if (class_exists('\Mappers\Soap\RelErabiltzaileak')) {

                $this->setMapper(new \Mappers\Soap\RelErabiltzaileak);

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
     * @return null | Mintzatu_Model_Validator_RelErabiltzaileak
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_RelErabiltzaileak')) {
            
                $this->setValidator(new Mintzatu_Validator_RelErabiltzaileak);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\RelErabiltzaileak::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdRel() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_rel = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdRel()));
    }
}
