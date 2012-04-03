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
class Mintzatu_Model_Raw_KarmaImgCategorias extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8)
     *
     * @var int
     */
    protected $_IdCategoria;

    /**
     * Database var type varchar(255)
     *
     * @var string
     */
    protected $_Nombre;

    /**
     * Database var type varchar(255)
     *
     * @var string
     */
    protected $_Url;



    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'idCategoria'=>'IdCategoria',
            'nombre'=>'Nombre',
            'url'=>'Url',
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
     * Sets column idCategoria
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaImgCategorias
     */
    public function setIdCategoria($data)
    {
        $this->_IdCategoria = $data;
        return $this;
    }

    /**
     * Gets column idCategoria
     *
     * @return int
     */
    public function getIdCategoria()
    {
 
        return $this->_IdCategoria;
    }


    /**
     * Sets column nombre
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaImgCategorias
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
     * Sets column url
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaImgCategorias
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
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\KarmaImgCategorias
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\KarmaImgCategorias')) {

                $this->setMapper(new \Mappers\Sql\KarmaImgCategorias);

            } else if (class_exists('\Mappers\Soap\KarmaImgCategorias')) {

                $this->setMapper(new \Mappers\Soap\KarmaImgCategorias);

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
     * @return null | Mintzatu_Model_Validator_KarmaImgCategorias
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_KarmaImgCategorias')) {
            
                $this->setValidator(new Mintzatu_Validator_KarmaImgCategorias);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\KarmaImgCategorias::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdCategoria() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('idCategoria = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdCategoria()));
    }
}
