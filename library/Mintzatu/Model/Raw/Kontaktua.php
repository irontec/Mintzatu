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
class Mintzatu_Model_Raw_Kontaktua extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdKontaktua;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Izena;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Posta;

    /**
     * Database var type text
     *
     * @var text
     */
    protected $_Mezua;

    /**
     * Database var type timestamp
     *
     * @var string
     */
    protected $_Noiz;



    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'id_kontaktua'=>'IdKontaktua',
            'izena'=>'Izena',
            'posta'=>'Posta',
            'mezua'=>'Mezua',
            'noiz'=>'Noiz',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('kontaktua'));

        $this->setParentList(array(
        ));

        $this->setDependentList(array(
        ));

		parent::__construct();
    }




    /**
     * Sets column id_kontaktua
     *
     * @param int $data
     * @return Mintzatu_Model_Kontaktua
     */
    public function setIdKontaktua($data)
    {
        $this->_IdKontaktua = $data;
        return $this;
    }

    /**
     * Gets column id_kontaktua
     *
     * @return int
     */
    public function getIdKontaktua()
    {
 
        return $this->_IdKontaktua;
    }


    /**
     * Sets column izena
     *
     * @param string $data
     * @return Mintzatu_Model_Kontaktua
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
     * Sets column posta
     *
     * @param string $data
     * @return Mintzatu_Model_Kontaktua
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
     * Sets column mezua
     *
     * @param text $data
     * @return Mintzatu_Model_Kontaktua
     */
    public function setMezua($data)
    {
        $this->_Mezua = $data;
        return $this;
    }

    /**
     * Gets column mezua
     *
     * @return text
     */
    public function getMezua()
    {
 
        return $this->_Mezua;
    }


    /**
     * Sets column noiz
     *
     * @param string $data
     * @return Mintzatu_Model_Kontaktua
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
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\Kontaktua
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\Kontaktua')) {

                $this->setMapper(new \Mappers\Sql\Kontaktua);

            } else if (class_exists('\Mappers\Soap\Kontaktua')) {

                $this->setMapper(new \Mappers\Soap\Kontaktua);

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
     * @return null | Mintzatu_Model_Validator_Kontaktua
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_Kontaktua')) {
            
                $this->setValidator(new Mintzatu_Validator_Kontaktua);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\Kontaktua::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdKontaktua() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_kontaktua = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdKontaktua()));
    }
}
