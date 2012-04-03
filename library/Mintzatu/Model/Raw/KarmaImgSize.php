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
class Mintzatu_Model_Raw_KarmaImgSize extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8)
     *
     * @var int
     */
    protected $_IdSize;

    /**
     * Database var type varchar(255)
     *
     * @var string
     */
    protected $_Nombre;

    /**
     * Database var type int(8)
     *
     * @var int
     */
    protected $_Height;

    /**
     * Database var type int(8)
     *
     * @var int
     */
    protected $_Width;



    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'idSize'=>'IdSize',
            'nombre'=>'Nombre',
            'height'=>'Height',
            'width'=>'Width',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array());

        $this->setParentList(array(
        ));

        $this->setDependentList(array(
        ));

		parent::__construct();
    }




    /**
     * Sets column idSize
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaImgSize
     */
    public function setIdSize($data)
    {
        $this->_IdSize = $data;
        return $this;
    }

    /**
     * Gets column idSize
     *
     * @return int
     */
    public function getIdSize()
    {
 
        return $this->_IdSize;
    }


    /**
     * Sets column nombre
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaImgSize
     */
    public function setNombre($data)
    {
        $this->_Nombre = $data;
        return $this;
    }

    /**
     * Gets column nombre
     *
     * @return string
     */
    public function getNombre()
    {
 
        return $this->_Nombre;
    }


    /**
     * Sets column height
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaImgSize
     */
    public function setHeight($data)
    {
        $this->_Height = $data;
        return $this;
    }

    /**
     * Gets column height
     *
     * @return int
     */
    public function getHeight()
    {
 
        return $this->_Height;
    }


    /**
     * Sets column width
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaImgSize
     */
    public function setWidth($data)
    {
        $this->_Width = $data;
        return $this;
    }

    /**
     * Gets column width
     *
     * @return int
     */
    public function getWidth()
    {
 
        return $this->_Width;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\KarmaImgSize
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\KarmaImgSize')) {

                $this->setMapper(new \Mappers\Sql\KarmaImgSize);

            } else if (class_exists('\Mappers\Soap\KarmaImgSize')) {

                $this->setMapper(new \Mappers\Soap\KarmaImgSize);

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
     * @return null | Mintzatu_Model_Validator_KarmaImgSize
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_KarmaImgSize')) {
            
                $this->setValidator(new Mintzatu_Validator_KarmaImgSize);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\KarmaImgSize::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdSize() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('idSize = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdSize()));
    }
}
