<?php

/**
 * Application Models
 *
 * @package Mintzatu_Model_Raw
 * @subpackage Model
 * @author <Arkaitz Etxeberria>
 * @copyright Irontec - Internet y Sistemas sobre GNU/Linux
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Abstract class for models
 */
require_once 'ModelAbstract.php';

/**
 * 
 *
 * @package Mintzatu_Model
 * @subpackage Model
 * @author <Arkaitz Etxeberria>
 */
class Mintzatu_Model_Raw_KategoriaIrudiak extends Mintzatu_Model_Raw_ModelAbstract
{

    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    public $_IdIrudia;

    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    public $_IdKategoria;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    public $_IrudiIzena;

    /**
     * Database var type int(50)
     *
     * @var int
     */
    public $_IrudiTamaina;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    public $_IrudiMota;


    /**
     * Parent relation kategoria_irudiak_ibfk_1
     *
     * @var Mintzatu_Model_Kategoriak
     */
    public $_Kategoriak;


    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'id_irudia'=>'IdIrudia',
            'id_kategoria'=>'IdKategoria',
            'irudi_izena'=>'IrudiIzena',
            'irudi_tamaina'=>'IrudiTamaina',
            'irudi_mota'=>'IrudiMota',
        ));

        $this->setParentList(array(
            'KategoriaIrudiakIbfk1'=> array(
                    'property' => 'Kategoriak',
                    'table_name' => 'Kategoriak',
                ),
        ));

        $this->setDependentList(array(
        ));
    }

    /**
     * Sets column id_irudia
     *
     * @param int $data
     * @return Mintzatu_Model_KategoriaIrudiak
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
     * Sets column id_kategoria
     *
     * @param int $data
     * @return Mintzatu_Model_KategoriaIrudiak
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
     * Sets column irudi_izena
     *
     * @param string $data
     * @return Mintzatu_Model_KategoriaIrudiak
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
     * @return Mintzatu_Model_KategoriaIrudiak
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
     * @return Mintzatu_Model_KategoriaIrudiak
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
     * Sets parent relation IdKategoria
     *
     * @param Mintzatu_Model_Kategoriak $data
     * @return Mintzatu_Model_KategoriaIrudiak
     */
    public function setKategoriak(Mintzatu_Model_Kategoriak $data)
    {
        $this->_Kategoriak = $data;

        $primary_key = $data->getPrimaryKey();
        if (is_array($primary_key)) {
            $primary_key = $primary_key['id_kategoria'];
        }

        $this->setIdKategoria($primary_key);

        return $this;
    }

    /**
     * Gets parent IdKategoria
     *
     * @param boolean $load Load the object if it is not already
     * @return Mintzatu_Model_Kategoriak
     */
    public function getKategoriak($load = true)
    {
        if ($this->_Kategoriak === null && $load) {
            $this->getMapper()->loadRelated('KategoriaIrudiakIbfk1', $this);
        }

        return $this->_Kategoriak;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\KategoriaIrudiak
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\KategoriaIrudiak')) {

                $this->setMapper(new \Mappers\Sql\KategoriaIrudiak);

            } else if (class_exists('\Mappers\Soap\KategoriaIrudiak')) {

                $this->setMapper(new \Mappers\Soap\KategoriaIrudiak);

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
     * @return null | Mintzatu_Model_Validator_KategoriaIrudiak
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_KategoriaIrudiak')) {
            
                $this->setValidator(new Mintzatu_Validator_KategoriaIrudiak);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\KategoriaIrudiak::delete
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
