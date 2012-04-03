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
class Mintzatu_Model_Raw_Orrialdeak extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdOrrialde;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Identifikatzailea;

    /**
     * Database var type varchar(350)
     *
     * @var string
     */
    protected $_Titulua;

    /**
     * Database var type text
     *
     * @var text
     */
    protected $_Edukia;

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
            'id_orrialde'=>'IdOrrialde',
            'identifikatzailea'=>'Identifikatzailea',
            'titulua'=>'Titulua',
            'edukia'=>'Edukia',
            'noiz'=>'Noiz',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('orrialde'));

        $this->setParentList(array(
        ));

        $this->setDependentList(array(
        ));

		parent::__construct();
    }




    /**
     * Sets column id_orrialde
     *
     * @param int $data
     * @return Mintzatu_Model_Orrialdeak
     */
    public function setIdOrrialde($data)
    {
        $this->_IdOrrialde = $data;
        return $this;
    }

    /**
     * Gets column id_orrialde
     *
     * @return int
     */
    public function getIdOrrialde()
    {
 
        return $this->_IdOrrialde;
    }


    /**
     * Sets column identifikatzailea
     *
     * @param string $data
     * @return Mintzatu_Model_Orrialdeak
     */
    public function setIdentifikatzailea($data)
    {
        $this->_Identifikatzailea = $data;
        return $this;
    }

    /**
     * Gets column identifikatzailea
     *
     * @return string
     */
    public function getIdentifikatzailea()
    {
 
        return $this->_Identifikatzailea;
    }


    /**
     * Sets column titulua
     *
     * @param string $data
     * @return Mintzatu_Model_Orrialdeak
     */
    public function setTitulua($data)
    {
        $this->_Titulua = $data;
        return $this;
    }

    /**
     * Gets column titulua
     *
     * @return string
     */
    public function getTitulua()
    {
 
        return $this->_Titulua;
    }


    /**
     * Sets column edukia
     *
     * @param text $data
     * @return Mintzatu_Model_Orrialdeak
     */
    public function setEdukia($data)
    {
        $this->_Edukia = $data;
        return $this;
    }

    /**
     * Gets column edukia
     *
     * @return text
     */
    public function getEdukia()
    {
 
        return $this->_Edukia;
    }


    /**
     * Sets column noiz
     *
     * @param string $data
     * @return Mintzatu_Model_Orrialdeak
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
     * @return \Mappers\Sql\Orrialdeak
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\Orrialdeak')) {

                $this->setMapper(new \Mappers\Sql\Orrialdeak);

            } else if (class_exists('\Mappers\Soap\Orrialdeak')) {

                $this->setMapper(new \Mappers\Soap\Orrialdeak);

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
     * @return null | Mintzatu_Model_Validator_Orrialdeak
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_Orrialdeak')) {
            
                $this->setValidator(new Mintzatu_Validator_Orrialdeak);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\Orrialdeak::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdOrrialde() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_orrialde = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdOrrialde()));
    }
}
