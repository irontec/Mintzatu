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
class Mintzatu_Model_Raw_KarmaImg extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8)
     *
     * @var int
     */
    protected $_IdImg;

    /**
     * Database var type int(8)
     *
     * @var int
     */
    protected $_IdCategoria;

    /**
     * Database var type varchar(255)
     *
     * @var string
     */
    protected $_Titulo;

    /**
     * Database var type mediumblob
     *
     * @var string
     */
    protected $_ImgBinario;

    /**
     * Database var type varchar(255)
     *
     * @var string
     */
    protected $_NombreImg;

    /**
     * Database var type varchar(50)
     *
     * @var string
     */
    protected $_SizeImg;



    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'idImg'=>'IdImg',
            'idCategoria'=>'IdCategoria',
            'Titulo'=>'Titulo',
            'img_binario'=>'ImgBinario',
            'nombre_img'=>'NombreImg',
            'size_img'=>'SizeImg',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('binario','img'));

        $this->setParentList(array(
        ));

        $this->setDependentList(array(
        ));

		parent::__construct();
    }




    /**
     * Sets column idImg
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaImg
     */
    public function setIdImg($data)
    {
        $this->_IdImg = $data;
        return $this;
    }

    /**
     * Gets column idImg
     *
     * @return int
     */
    public function getIdImg()
    {
 
        return $this->_IdImg;
    }


    /**
     * Sets column idCategoria
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaImg
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
     * Sets column Titulo
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaImg
     */
    public function setTitulo($data)
    {
        $this->_Titulo = $data;
        return $this;
    }

    /**
     * Gets column Titulo
     *
     * @return string
     */
    public function getTitulo()
    {
 
        return $this->_Titulo;
    }


    /**
     * Sets column img_binario
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaImg
     */
    public function setImgBinario($data)
    {
        $this->_ImgBinario = $data;
        return $this;
    }

    /**
     * Gets column img_binario
     *
     * @return string
     */
    public function getImgBinario()
    {
 
        return $this->_ImgBinario;
    }


    /**
     * Sets column nombre_img
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaImg
     */
    public function setNombreImg($data)
    {
        $this->_NombreImg = $data;
        return $this;
    }

    /**
     * Gets column nombre_img
     *
     * @return string
     */
    public function getNombreImg()
    {
 
        return $this->_NombreImg;
    }


    /**
     * Sets column size_img
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaImg
     */
    public function setSizeImg($data)
    {
        $this->_SizeImg = $data;
        return $this;
    }

    /**
     * Gets column size_img
     *
     * @return string
     */
    public function getSizeImg()
    {
 
        return $this->_SizeImg;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\KarmaImg
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\KarmaImg')) {

                $this->setMapper(new \Mappers\Sql\KarmaImg);

            } else if (class_exists('\Mappers\Soap\KarmaImg')) {

                $this->setMapper(new \Mappers\Soap\KarmaImg);

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
     * @return null | Mintzatu_Model_Validator_KarmaImg
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_KarmaImg')) {
            
                $this->setValidator(new Mintzatu_Validator_KarmaImg);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\KarmaImg::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdImg() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('idImg = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdImg()));
    }
}
